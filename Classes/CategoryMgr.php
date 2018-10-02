<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Class categoryMgr
//
//  This is a manager class for claa category
//
//  @AUTHOR: Tony O'Dowd
//
//  @NOTES: This is based on standard Bayesian and SRL modelling techniques.
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Class CategoryMgr
 */
class CategoryMgr
{
    public $listCategory = [];
    public $numberCategory;
    // using this to version stamp SVM models. Useful for debugging
    public $versionNumber = "1.0";

    /**
     * CategoryMgr constructor.
     */
    public function __construct()
    {
        $this->numberCategory = 0;
    }

    /**
     * @param $catname
     * @param $wordAlignment
     */
    public function addCategory($catname, $wordAlignment)
    {
        $cat = new Category($catname, $wordAlignment);
        if (!isset($this->listCategory[$catname])) {
            $this->listCategory[$catname] = $cat;
            $this->numberCategory++;
        } else {
            die("error : Category does not exist! Please check!");
        }
    }

    /**
     * @return int
     */
    public function getAllWordCount()
    {
        $numWC = 0;
        foreach ($this->listCategory as $obj) {
            $numWC += $obj->numberWordsInCategory;
        }

        return $numWC;
    }

    /**
     * @param $catname
     * @return float|int
     */
    public function getCatProb($catname)
    {
        return $this->getAllWordCount() > 0 ? $this->listCategory[$catname]->numberWordsInCategory / $this->getAllWordCount() : 0;
    }
}
