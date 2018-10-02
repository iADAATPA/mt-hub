<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();

$engineId = empty($_POST['id']) ? Session::getActiveEngineId() : $_POST['id'];
$engines = new Engines($engineId);
Session::setActiveEngineId($engineId);

return true;
