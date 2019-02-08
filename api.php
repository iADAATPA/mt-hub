<?php
/**
 * User: marek
 * Date: 09/11/2017
 */
include_once 'functions.php';

$container = ApiContainer::getContainer();
$app = new \Slim\App($container);

// API
$app->group('/api', function () {
    // Version 2
    $this->group('/v2', function () {
        $this->get('/aretrievetranslation/{token}/{guid}', 'Api:aRetrieveTranslation');
        $this->get('/retrievetranslation/{token}/{guid}', 'Api:aRetrieveTranslation');
        $this->post('/atranslate', 'Api:aTranslate');
        $this->post('/atranslatewithqes', 'Api:aTranslate');
        $this->get('/describesuppliers/{token}', 'Api:describeSuppliers');
        $this->get('/describelanguages/{token}', 'Api:describeLanguages');
        $this->post('/detectdomain', 'Api:detectDomain');
        $this->post('/detectlanguage', 'Api:detectLanguage');
        $this->post('/retrievefiletranslation', 'Api:retrieveFileTranslation');
        $this->post('/aretrievefiletranslation', 'Api:retrieveFileTranslation');
        $this->post('/translate', 'Api:translate');
        $this->post('/translatebestof', 'Api:translateBestOf');
        $this->post('/atranslatebestof', 'Api:translateBestOf');
        $this->post('/translatefile', 'Api:translateFile');
        $this->post('/atranslatefile', 'Api:translateFile');
        $this->post('/translatewithqes', 'Api:TranslateWithQes');
    });

    // Dev version
    $this->group('/dev', function () {
        $this->get('/aretrievetranslation/{token}/{guid}', 'ApiDev:aRetrieveTranslation');
        $this->get('/aretrievetranslationwithqes/{token}/{guid}', 'ApiDev:aRetrieveTranslationWithQes');
        $this->get('/retrievetranslation/{token}/{guid}', 'ApiDev:aRetrieveTranslation');
        $this->post('/atranslate', 'ApiDev:aTranslate');
        $this->post('/atranslatewithqes', 'ApiDev:aTranslate');
        $this->get('/describesuppliers/{token}', 'ApiDev:describeSuppliers');
        $this->get('/describelanguages/{token}', 'ApiDev:describeLanguages');
        $this->post('/detectdomain', 'ApiDev:detectDomain');
        $this->post('/detectlanguage', 'ApiDev:detectLanguage');
        $this->post('/retrievefiletranslation', 'ApiDev:retrieveFileTranslation');
        $this->post('/aretrievefiletranslation', 'ApiDev:retrieveFileTranslation');
        $this->post('/translate', 'ApiDev:translate');
        $this->post('/translatebestof', 'ApiDev:translateBestOf');
        $this->post('/atranslatebestof', 'ApiDev:translateBestOf');
        $this->post('/translatefile', 'ApiDev:translateFile');
        $this->post('/atranslatefile', 'ApiDev:translateFile');
        $this->post('/translatewithqes', 'ApiDev:TranslateWithQes');
    });

    $this->get('/aretrievetranslation/{token}/{guid}', 'Api:aRetrieveTranslation');
    $this->get('/aretrievetranslationwithqes/{token}/{guid}', 'Api:aRetrieveTranslationWithQes');
    $this->get('/retrievetranslation/{token}/{guid}', 'Api:aRetrieveTranslation');
    $this->post('/atranslate', 'Api:aTranslate');
    $this->post('/atranslatewithqes', 'Api:aTranslate');
    $this->get('/describesuppliers/{token}', 'Api:describeSuppliers');
    $this->get('/describelanguages/{token}', 'Api:describeLanguages');
    $this->post('/detectdomain', 'Api:detectDomain');
    $this->post('/detectlanguage', 'Api:detectLanguage');
    $this->post('/retrievefiletranslation', 'Api:retrieveFileTranslation');
    $this->post('/aretrievefiletranslation', 'Api:retrieveFileTranslation');
    $this->post('/translate', 'Api:translate');
    $this->post('/translatebestof', 'Api:translateBestOf');
    $this->post('/atranslatebestof', 'Api:translateBestOf');
    $this->post('/translatefile', 'Api:translateFile');
    $this->post('/atranslatefile', 'Api:translateFile');
    $this->post('/translatewithqes', 'Api:TranslateWithQes');
});

$app->run();
