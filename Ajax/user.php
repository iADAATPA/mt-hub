<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$userId = empty($_POST['id']) ? Session::getUserId() : $_POST['id'];
$users = new Users($userId);
$usersInitial = clone($users);
$email = empty($_POST['email']) ? $users->getEmail() : $_POST['email'];
$oldPassword = empty($_POST['password']) ? null : Helper::sanitizeString($_POST['password']);
$newPassword = empty($_POST['newPassword']) ? null : Helper::sanitizeString($_POST['newPassword']);
$reenteredPassword = empty($_POST['reenteredPassword']) ? null : Helper::sanitizeString($_POST['reenteredPassword']);

if (empty($email)) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Please enter your email'));
} elseif ($oldPassword && $newPassword && !$reenteredPassword) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Please re-enter your new password to update it'));
} elseif ($oldPassword && !$newPassword && $reenteredPassword) {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Please enter your new password to update it'));
} elseif ($oldPassword && $newPassword && $reenteredPassword) {
    if (!$users->verifyPassword($oldPassword)) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('Incorrect current password.'));
    } elseif ($users->verifyPassword($newPassword)) {
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
    }
}

if ($returnCalls->getStatusId() == ReturnCalls::STATUSID_SUCCESS) {
    $returnCalls->setMessage(Session::t('User settings updated.'));
    $users->setEmail($email);
    $response = $users->update();

    if (!$response) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    } else {
        Log::save(Log::USER_UPDATED, $userId, Log::getObjectDifferences($usersInitial, $users));
    }
}

$returnCalls->getResponse();
