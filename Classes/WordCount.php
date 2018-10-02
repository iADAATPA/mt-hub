<?php

/**
 * Class WordCount
 */
class WordCount
{
    public static function countWords($sourceLang, $text)
    {
        $wordCount = 0;

        if (!is_array($text)) {
            $text[] = $text;
        }

        foreach ($text as $string) {
            // Exclude tags
            $string = strip_tags(urldecode($string));

            // Calc wordcount WC : Determine if DBCS language or SBC language. (If DBCS return character count, otherwise return word count
            if (preg_match("/ja|jp|zh|cn|tw/", $sourceLang)) {
                $stringNoSpace = preg_replace('/\s+/', '', $string);
                $wordCount += mb_strlen($stringNoSpace, "UTF-8");
            } else {
                $previousChar = null;
                $currentChar = null;
                $convertedString = str_split($string);
                for ($i = 0; $i < sizeof($convertedString) - 1; $i++) {
                    $previousChar = $convertedString[$i];
                    $currentChar = $convertedString[$i + 1];
                    if (!ctype_space($previousChar) && ctype_space($currentChar)) {
                        $wordCount++;
                    }
                }
                if (!ctype_space($previousChar) && !ctype_space($currentChar)) {
                    $wordCount++;
                }
            }
        }

        return $wordCount;
    }
}
