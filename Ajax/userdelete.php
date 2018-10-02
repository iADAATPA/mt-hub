<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();
$returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
$returnCalls->setMessage(Session::t('An unexpected error occurred'));

$id = empty($_POST['id']) ? null : $_POST['id'];
$users = new Users($id);

if ($id) {
    $response = $users->delete();

    if ($response) {
        Log::save(Log::USER_DELETED, $id);
        // Check if the user was an account admin
        $accountId = $users->getAccountId();
        $accounts = new Accounts($accountId);
        $accountsInitial = clone($accounts);

        if ($accounts->getAdminId() == $id) {
            $accounts->setAdminId(null);
            $accounts->update();

            Log::save(Log::ACCOUNT_UPDATED, $accountId, Log::getObjectDifferences($accountsInitial, $accounts));

            $returnCalls->setMessage(Session::t('User has been deleted and removed as an account') . ' [#' . $accountId . '] ' . Session::t('administrator.'));
        } else {
            $returnCalls->setMessage(Session::t('User has been deleted.'));
        }

        $returnCalls->setStatusId(ReturnCalls::STATUSID_SUCCESS);
    }
}

$returnCalls->getResponse();
