<?php

include_once '../functions.php';

$filename = "domains.svm";


#$tr = new Trainer( $filename, trainer::$WordAlignment );
#$arr = array(       "public_administration", "segittur" );
#$tr->compileCategory($arr, $filename );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  UNIT TEST for most Domain Detection 
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo "#################################################################################\n";
echo "##                                                                             ##\n";
echo "##                   Unit tests for Domain Detection                           ##\n";
echo "##                                                                             ##\n";
echo "#################################################################################\n\n";

/*
 * Now lets see which category the text comes from
 */
$cl = new Classifier($path = getDirectory() . 'Classifier/Domains/' . Session::getAccountId() . '/' . $filename );


// segittur
$str = "Tendra como objetivo contribuir a acelerar y consolidar las distintas fases del crecimiento internacional de las pymes turisticas españolas. Surge del sector asociado que busca desarrollar nuevos mecanismos de cooperacion entre las diferentes artes interesadas en el desarrolo del turismo accesible desde la optica de la innovacion y la colaboracion publico-priva Nuevas tecnologías para la promoción del turismo cultural.";
detectDomain( "segittur", $cl, $str );


// public_administration
$str = "La tasa de variación interanual de los precios de los alimentos no elaborados se ha reducido 0,3 puntos en enero, hasta el -0,7%.
Al igual que en años anteriores, el Tesoro recurrirá a sindicaciones bancarias para colocar determinadas referencias, aunque las subastas seguirán siendo el principal método de emisión de Deuda del Estado.
En media anual, el porcentaje de parados sobre población activa será del 19,8% en 2016 para alcanzar el 15,6% en 2018.
Esta bajada0 se explica por un menor optimismo de las empresas exportadoras en el trimestre de referencia y de sus expectativas de exportación a corto y a medio plazo, coincidiendo con los meses estivales.";
detectDomain( "Public_Administration", $cl, $str );

echo "\n\nDone!\n";



function detectDomain( $domain, $cl, $str )
{
    $classified = $cl->classifyText($str);
    echo "----------------------------------------------------------------------------------------------------------------------------------------\n";
    echo "Domain   : [$domain]\n";
    echo "String   : [" . substr($str, 0, 120) . "..." . "]\n";
    echo "Result   : [" . $classified . "]\n";
    echo "Status   : [" . (strcasecmp($domain, $classified) ? "ERROR : Classifier failure!!!" : "OK" ) . "]\n";
}

?>