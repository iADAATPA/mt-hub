<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  Command Line Compiler for QES Verctor Spaces.
//
//  1st Param - Name of SVM Model and Folder location in cat\ of sample translations
//
//  @AUTHOR: Tony O'Dowd
//
////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once '..\Classes\class.tokencount.php';
include_once '..\Classes\class.tokenprob.php';
include_once '..\Classes\class.category.php';
include_once '..\Classes\class.categorymgr.php';
include_once '..\Classes\class.classifier.php'; 
include_once '..\Classes\class.trainer.php';

echo "Compiler for QES Vector Spaces - Version 1.0 \nAuthor: Tony O'Dowd\n";

// Simple check for commaind line parammters
if ($argc != 2 || !file_exists( "cat\\$argv[1]") )
{
    echo "[ERROR] Cannot locate folder containing Vector Model training data: [cat\\$argv[1]]\n";
    exit;
}

echo "Compiling model....\n";

$filename = $argv[1] . ".svm";

$tr = new Trainer( $filename, trainer::$WordAlignment );

$arr = array( "$argv[1]" );

$tr->compileCategory($arr, $filename );

echo "\n\nDone!";