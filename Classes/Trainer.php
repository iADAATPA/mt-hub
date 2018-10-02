<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Class trainer
//
//  This class is used to train the Bayesian model using SRL techniques. It simmply calculates the probability of
//  tokens/words across a corpus and stores them in class category. We then group categories together with class
//  categoryMgr. We then serialise this class to a disk file (*.svm). You need only run the trainer once on your
//  corpus and once it's stored in an *.svm file you can simple use the claa classifier to load this file and
//  classify your texts.
//
//  This class requires the following files to be available :-
//
//  (1) There should be a folder called cat.
//  (2) In this folder, create sub-folders using the name of the classified text. ( eg Pulic_Administration)
//  (3) In this folder store your training data ( a file *.txt) that you want to use to build the SVM 
//  
//  NOTE: The reason I've adopted this approach is that this is the simplest way to manage training data for the
//        classifier and trainer. Make sure all of your training files are UTF-8 encoded, otherwise you'll get
//        strange results.
//
//  @AUTHOR: Tony O'Dowd
//
//  @NOTES: This is based on standard Bayesian and SRL modelling techniques.
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Class Trainer
 */
class Trainer
{

    public static $noWordAlignment = false;
    public static $WordAlignment = true;

    // This is a list of words that you want to exclude from the modelling process.
    private $excludedWords = ['this', 'that', 'those', 'on', 'an', 'I'];

    public $ctgrMgr;
    public $arrWc = [];
    public $debug = false;
    public $wordAlignment;
    public $svmModel = "domains.svm";
    private $pathToSvnModel = null;
    private $data = null;

    /**
     * Trainer constructor.
     * @param string $model
     * @param bool $alignment
     */
    public function __construct($pathToSvnModel = null, $alignment = true)
    {
        $this->setCtgrMgr(new CategoryMgr());
        $this->setWordAlignment($alignment);
        $this->setPathToSvnModel($pathToSvnModel);
    }

    /**
     * @param $category
     */
    public function compileCategory($category)
    {
        // Traing rach category
        if (is_array($category)) {
            foreach ($category as $val) {
                $this->trainNewCategory($val, $this->getPathToSvnModel() . $val);
            }

            // Compute token probality
            foreach ($category as $val) {
                if (!empty($this->arrWc[$val])) {
                    foreach ($this->arrWc[$val]->tokens as $key => $row) {
                        $prob = (1 + $row) / ($this->ctgrMgr->listCategory[$val]->numberWordsInCategory + $this->arrWc[$val]->numberTokens);
                        //echo "$row ";
                        $this->ctgrMgr->listCategory[$val]->addWordProb($key, $prob);
                    }

                    if ($this->debug) {
                        echo "Stats for [ $val ]\n";
                        echo "\tNumber in category : [ " . $this->ctgrMgr->listCategory[$val]->numberWordsInCategory . " ]   ";
                        echo "Number vocab : [ " . $this->arrWc[$val]->numberTokens . " ]   ";
                        echo "Probability : [ " . $this->ctgrMgr->getCatProb($val) . " ]\n\n";
                    }
                }
            }

            return $this->serializeCat();
        } else {
            return false;
        }
    }

    /**
     * @param $category
     */
    public function compile()
    {
        // Traing rach category
        if (!empty($this->getData()) && is_array($this->getData())) {
            foreach ($this->getData() as $name => $data) {
                $this->ctgrMgr->addCategory($name, $this->wordAlignment);
                $this->trainData($name, $data);
            }

            // Compute token probality
            foreach ($this->getData() as $name => $data) {
                if (!empty($this->arrWc[$name])) {
                    foreach ($this->arrWc[$name]->tokens as $key => $row) {
                        $prob = (1 + $row) / ($this->ctgrMgr->listCategory[$name]->numberWordsInCategory + $this->arrWc[$name]->numberTokens);
                        $this->ctgrMgr->listCategory[$name]->addWordProb($key, $prob);
                    }

                    error_log("Stats for [" . $name . "]");
                    error_log("Number in category : [" . $this->ctgrMgr->listCategory[$name]->numberWordsInCategory . "]");
                    error_log("Number vocab : [" . $this->arrWc[$name]->numberTokens . "]");
                    error_log("Probability : [" . $this->ctgrMgr->getCatProb($name) . "]");
                }
            }

            return serialize($this->ctgrMgr);
        } else {
            return false;
        }
    }

    /**
     * @param $catName
     * @param $dir
     */
    public function trainNewCategory($catName, $dir)
    {
        $this->ctgrMgr->addCategory($catName, $this->wordAlignment);

        if (file_exists($dir) && $d = opendir($dir)) {
            $data = '';
            $numberFile = 0;
            while ($f1 = readdir($d)) {
                $path = $dir . '/' . $f1;
                $info = pathinfo($path);

                if (is_file($path) && !empty($info['extension']) && ($info['extension'] == 'txt')) {
                    $data = file_get_contents($path) . $data;

                    $numberFile++;
                }
            }

            if (($numberFile == 0) || (strlen($data) == 0)) {
                return false;
            }

            $this->trainData($catName, $data);
            closedir($d);
        }

        return true;
    }

    /**
     * @param $catname
     * @param $data
     */
    public function trainData($catname, $data)
    {
        $countTokens = new TokenCount();
        $counter = 0;

        // Check wordalignment
        if (!$this->wordAlignment) {
            // If wordalignment is false, put a space between each character
            // This builds character based models, rather than word based models
            // NOTE: (*UTF8) switched the regex into UTF8 model IMPORTANT for DBCS and Languages
            // other than english.
            $data = preg_replace('/(\w)/u', '$1 ', $data);
        }

        // create array of words/characters
        $tokens = preg_split('/\s+/u', $data, -1, PREG_SPLIT_NO_EMPTY);

        if (is_array($tokens)) {
            foreach ($tokens as $token) {
                if (!in_array($token, $this->excludedWords)) {
                    $countTokens->addToken($token);
                    $counter++;
                }
            }
        }

        /* Update total number of tokens in a category */
        $this->ctgrMgr->listCategory[$catname]->incNumberWord($counter);

        $this->ctgrMgr->listCategory[$catname]->numberVocab = $countTokens->numberTokens;

        $this->arrWc[$catname] = $countTokens;
    }

    public function serializeCat()
    {
        $ser = serialize($this->ctgrMgr);
        $fp = fopen($this->getPathToSvnModel() . $this->getSvmModel(), 'w');

        if ($fp === false) {
            return false;
        } else {
            fwrite($fp, $ser);
            fclose($fp);

            return true;
        }
    }

    /**
     * @return string
     */
    public function getSvmModel()
    {
        return $this->svmModel;
    }

    /**
     * @param string $svmModel
     */
    public function setSvmModel($svmModel)
    {
        $this->svmModel = $svmModel;
    }

    /**
     * @return null
     */
    public function getPathToSvnModel()
    {
        return $this->pathToSvnModel;
    }

    /**
     * @param null $pathToSvnModel
     */
    public function setPathToSvnModel($pathToSvnModel)
    {
        $this->pathToSvnModel = $pathToSvnModel;
    }

    /**
     * @return CategoryMgr
     */
    public function getCtgrMgr()
    {
        return $this->ctgrMgr;
    }

    /**
     * @param CategoryMgr $ctgrMgr
     */
    public function setCtgrMgr($ctgrMgr)
    {
        $this->ctgrMgr = $ctgrMgr;
    }

    /**
     * @return bool
     */
    public function isWordAlignment()
    {
        return $this->wordAlignment;
    }

    /**
     * @param bool $wordAlignment
     */
    public function setWordAlignment($wordAlignment)
    {
        $this->wordAlignment = $wordAlignment;
    }

    /**
     * @return bool
     */
    public static function isNoWordAlignment()
    {
        return self::$noWordAlignment;
    }

    /**
     * @param bool $noWordAlignment
     */
    public static function setNoWordAlignment($noWordAlignment)
    {
        self::$noWordAlignment = $noWordAlignment;
    }

    /**
     * @return array
     */
    public function getExcludedWords()
    {
        return $this->excludedWords;
    }

    /**
     * @param array $excludedWords
     */
    public function setExcludedWords($excludedWords)
    {
        $this->excludedWords = $excludedWords;
    }

    /**
     * @return array
     */
    public function getArrWc()
    {
        return $this->arrWc;
    }

    /**
     * @param array $arrWc
     */
    public function setArrWc($arrWc)
    {
        $this->arrWc = $arrWc;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return null
     */
    public function getData()
    {
        if (!empty($this->data)) {
            return $this->data;
        } else {
            return json_encode(["0" => ""]);
        }
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
