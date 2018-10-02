<?php

include_once '../functions.php';

$filename = "languages.svm";

/////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Create trainer, make sure we're using no wordalignment. Language models are character based!
//
////////////////////////////////////////////////////////////////////////////////////////////////////

    $tr = new Trainer( $filename, trainer::$noWordAlignment );

////////////////////////////////////////////////////////////////////////////////////////////////////
//
// put all of the language names here. Put your training data in each of these folders. 
//
////////////////////////////////////////////////////////////////////////////////////////////////////

    $arr = array(   "af",               // Afrikaan
                    "ar",               // arabic
                    "bg",               // bulgarian
                    "es-ES",            // spanish-spain (castilian Spanis)
                    "zh-CN",            // Simplified Chinese
                    "cs",               // Czech
                    "da",               // Danish
                    "nl",               // Dutch
                    "en",               // English
                    "et",               // Estonian
                    "fi",               // Finish
                    "fr",               // French
                    "gl-ES",            // Glacian Spanish
                    "de",               // German
                    "el",               // Greek
                    "hu",               // Hungarian
                    "gd-IE",            // Irish - Ireland
                    "it",               // Italian
                    "lv",               // Latvian
                    "lt",               // Lithuanian
                    "mt",               // Maltese
                    "ja-JP",            // Japan
                    "ko",               // Korean
                    "pl",               // Polish
                    "pt-PT",            // Portuguguese-Portugal
                    "ro",               // Romamian
                    "ru",               // Russian
                    "sk",               // Slovak
                    "sl",               // Slovian
                    "es",               // Spainish - Spain
                    "sv"                // Swedish
            );

////////////////////////////////////////////////////////////////////////////////////////////////////
//
// Model compilation happens now!
//
////////////////////////////////////////////////////////////////////////////////////////////////////

        $tr->compileCategory($arr, $filename );


// That's it. All done!
        
echo "Created SVM [$filename].\n";

echo "\n\nDone!";