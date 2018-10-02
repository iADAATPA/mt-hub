<?php

include_once 'functions.php';

Session::authenticateUser();

$returnCalls = new ReturnCalls();

$domainDataId = empty($_GET['id']) ? null : $_GET['id'];
$domainData = new DomainData($domainDataId);

if ($domainData->getAccountId() == Session::getAccountId()) {
    $domains = new Domains($domainData->getDomainId());

    header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename=" . $domains->getName() . ".txt");

    echo $domainData->getSegments();
}

exit();
