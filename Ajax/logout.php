<?php

include_once '../functions.php';

// Log the event
Log::save(Log::USER_LOGOUT);

// Destroy session
Session::destroySession();

return;
