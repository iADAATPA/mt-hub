<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Class tokenProb
//
// Simple class to store a token and it's probaility. These are used to classify texts later by class classifier
//
//  @AUTHOR: Tony O'Dowd
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Class TokenProb
 */
class TokenProb
{
    /**
     * @var array
     */
    public $words = [];

    /**
     * TokenProb constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $word
     * @param $prob
     */
    public function addProb($word, $prob)
    {
        $this->words[$word] = $prob;
    }
}
