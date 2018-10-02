<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Class tokencount
//
//  This class stores tokens and their count values within a corpus. This is used to determine probability
//  scores later.
//
//  @AUTHOR: Tony O'Dowd
//
//  @NOTES: This is based on standard Bayesian and SRL modelling techniques.
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Class Tokencount
 */
class TokenCount
{
    public $numberTokens;
    public $tokens = [];

    /**
     * Tokencount constructor.
     */
    public function __construct()
    {
        $numberTokens = 0;
    }

    /**
     * @param $token
     */
    public function addToken($token)
    {
        if (isset($this->tokens[$token])) {
            $this->tokens[$token]++;
        } else {
            $this->tokens[$token] = 1;
            $this->numberTokens++;
        }
    }

    public function deltokens()
    {
        unset($tokens);
        $this->numberTokens = 0;
    }
}
