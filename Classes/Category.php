<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Class category
//
//  The category class is used to define an object of a certain classified type. It contains a list of
//  tokens (or words) with the claculated probabilities compuyted against the total number of tokens/words
//  in the category.
//
//  @AUTHOR: Tony O'Dowd
//
//  @NOTES: This is based on standard Bayesian and SRL modelling techniques.
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Class Category
 */
class Category
{
    public $numberWordsInCategory;
    public $categoryProb;
    public $nameOfCategory;
    public $wordProb;
    public $wordAlignment;
    public $numberVocab;

    /**
     * Category constructor.
     * @param $name
     * @param bool $wordAlignment
     */
    public function __construct($name, $wordAlignment = true)
    {
        $this->wordProb = new TokenProb();
        $this->numberWordsInCategory = 0;
        $this->categoryProb = 0.5;
        $this->nameOfCategory = $name;
        $this->wordAlignment = $wordAlignment;
    }

    /**
     * @param $inc
     */
    public function incNumberWord($inc)
    {
        $this->numberWordsInCategory += $inc;
    }

    /**
     * @param $token
     * @param $prob
     */
    public function addWordProb($token, $prob)
    {
        $this->wordProb->addProb($token, $prob);
    }
}
