<?php

include_once '../functions.php';

$newPassword = empty($_POST['newPassword']) ? null : Helper::sanitizeString($_POST['newPassword']);
$reenteredPassword = empty($_POST['reenteredPassword']) ? null : Helper::sanitizeString($_POST['reenteredPassword']);
$token = empty($_POST['token']) ? null : Helper::sanitizeString($_POST['token']);
$returnCalls = new ReturnCalls();

if (empty($token)) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Token expired or invalid.'));
} elseif (empty($newPassword) || empty($reenteredPassword)) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('You must enter a new password.'));
} else {
    $users = new Users(null, null, $token);
    $accountId = $users->getAccountId();
    // If the accountId is empty then the token was incorrect.
    if (empty($accountId)) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('Token expired or invalid.'));
    } else {
        $verified = $users->verifyPassword($newPassword);

        if ($verified) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('You cannot reuse your old password.'));
        } elseif ($newPassword != $reenteredPassword) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('New passwords don&apos;t match.'));
        } elseif (strlen($newPassword) < 8) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Password too short.'));
        } elseif (preg_match('/[a-z]/', $newPassword) == 0) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Password must contain at least one lowercase letter.'));
        } elseif (preg_match('/[A-Z]/', $newPassword) == 0) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Password must contain at least one uppercase letter.'));
        } elseif (preg_match('/\d/', $newPassword) == 0) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Password must contain at least one number.'));
        } else {
            $users->setPassword($newPassword);
            $users->setToken(null);
            $users->update();

            // Log the event
            Log::save(Log::USER_SET_PASSWORD);

            $returnCalls->setMessage(Session::t('Password set successfully.'));
        }
    }
}

// Return the error status
$returnCalls->getResponse();
