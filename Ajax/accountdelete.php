<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();

$password = empty($_POST['password']) ? null : $_POST['password'];
$check = empty($_POST['check']) ? null : $_POST['check'];
$id = empty($_POST['id']) ? null : $_POST['id'];

if ($check) {
    $users = new Users(Session::getUserId());

    if (!$users->verifyPassword($password)) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('Incorrect password.'));
    }
} elseif ($id) {
    $accounts = new Accounts(Session::getAccountId());
    $accounts->setActive(null);
    $accounts->setDeleted(date('Y-m-d H:i:s', time()));
    $response = $accounts->update();

    if ($response) {
        Log::save(Log::ACCOUNT_DELETED);

        $users = new Users(Session::getUserId());
        $users->setAccountId(Session::getAccountId());
        $users->delete();

        Log::save(Log::USER_DELETED, Session::getUserId());

        $accountUsers = $users->getAccountUsers();

        if ($accountUsers && is_array($accountUsers)) {
            foreach ($accountUsers as $user) {
                $users = new Users($user['id']);
                $users->delete();

                Log::save(Log::USER_DELETED, $user['id']);
            }
        }
    } else {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('An unexpected error occurred'));
    }
} else {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('An unexpected error occurred'));
}

$returnCalls->getResponse();
