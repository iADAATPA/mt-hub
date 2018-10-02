<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Class classifier
//
//  This class is the classifier for class category (and class categorngr). This class will classifier a text against
//  standard Bayesian models (stored in *.svm files). This class computes the log sum of all the probailities of tokens
//  in the texts and returns the highest score to determine the type.
//
//  @AUTHOR: Tony O'Dowd
//
//  @NOTES: This is based on standard Bayesian and SRL modelling techniques.
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Class Classifier
 */
class Classifier
{
    public $catMgr;

    /**
     * Classifier constructor.
     * @param $svmModel
     */
    public function __construct($svmModelUrl = null, $txtCatMgr = null)
    {
        if ($svmModelUrl) {
            $txtCatMgr = file_exists($svmModelUrl) ? file_get_contents($svmModelUrl) : null;
        }

        $this->catMgr = unserialize($txtCatMgr);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function classifyFile($path)
    {
        $datafile = file_get_contents($path);
        return $this->classifyText($datafile);
    }

    /**
     * @param $text
     * @return mixed
     */
    public function classifyText($text)
    {
        $name = [];
        $value = [];
        $cat = !empty($this->catMgr->listCategory) ? $this->catMgr->listCategory : null;

        $i = 0;
        $max = 0;

        if (is_array($cat)) {
            foreach ($cat as $obj_cat) {
                $value[$i] = $this->computeProb($text, $obj_cat->nameOfCategory);
                $name[$i] = $obj_cat->nameOfCategory;
                $i++;
            }

            $i = 1;

            while ($i < count($value)) {
                if ($value[$max] < $value[$i]) {
                    $max = $i;
                }
                $i++;
            }
        }

        return !empty($name[$max]) ? $name[$max] : null;
    }

    /**
     * @param $text
     * @param $catname
     * @return float|int
     */
    public function computeProb($text, $catname)
    {
        // Check word alignment of the Category
        if (!$this->catMgr->listCategory[$catname]->wordAlignment) {
            // If wordalignment is false, put a space between each character
            // This builds character based models, rather than word based models
            $text = preg_replace('/(\w)/u', '$1 ', $text);
        }
        $data = explode(' ', $text);
        if (!isset($this->catMgr->listCategory[$catname])) {
            die ("error : Category '$catname' doesn't exist.");
        }

        $wordprb = $this->catMgr->listCategory[$catname]->wordProb->words;
        $probability = 0;

        foreach ($data as $txt) {
            if (isset($wordprb[$txt])) {
                $probability += log($wordprb[$txt]);
            } else {
                $num = ($this->catMgr->listCategory[$catname]->numberWordsInCategory + $this->catMgr->listCategory[$catname]->numberVocab);

                if ($num > 0) {
                    $probability += log(1 / $num);
                }
            }
        }

        $probability += log($this->catMgr->getCatProb($catname));

        return $probability;
    }
}
