<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();
$returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
$returnCalls->setMessage(Session::t('An unexpected error occurred'));

$id = empty($_POST['id']) ? null : $_POST['id'];
$accounts = new Accounts($id);
$accountsInitial = clone($accounts);
$adminId = empty($_POST['adminid']) ? $accounts->getAdminId() : $_POST['adminid'];
$active = empty($_POST['active']) ? false : true;
$groupId = empty($_POST['groupid']) ? $accounts->getGroupId() : $_POST['groupid'];
$name = empty($_POST['name']) ? $accounts->getName() : $_POST['name'];
$adminEmail = empty($_POST['adminEmail']) ? null : $_POST['adminEmail'];
$userName = empty($_POST['userName']) ? null : $_POST['userName'];

if ($adminEmail && !Mail::isValidEmail($adminEmail)) {
    $returnCalls->setMessage(Session::t('Please enter a valid Admin Email.'));
} elseif (empty($name)) {
    $returnCalls->setMessage(Session::t('Please enter an account Name.'));
} else {
    if ($adminEmail && $userName) {
        $users = new Users(null, $userName);

        if (!$users->getAccountId()) {
            $users->setEmail($adminEmail);
            $users->setName($userName);
            $token = Users::generateToken();
            $users->setToken($token);
            $adminId = $users->insert();

            if ($adminId) {
                Log::save(Log::USER_ADDED, $adminId);

                $users->setId($adminId);
                $users->update();

                $mail = new Mail();
                $mail->sendHtmlEmail(
                    $adminEmail,
                    Session::t('New Account created.'),
                    Session::t('Hi') . ' ' . $userName . '<br/><br/>' . Session::t('Please set up your password to the IADAATPA platform by clicking on the following link:') . ' <a href="https://iadaatpa.eu?token=' . $token . '">www.iadaatpa.eu</a>.<br/><br/>' . Session::t('Your User Name is:') . ' ' . $userName . '<br/><br/><br/><br/>IADAATPA Team'
                );
            }
        } else {
            $returnCalls->setMessage(Session::t('Please choose another Username.'));
        }
    }

    if ($adminId) {
        $accounts->setName($name);
        $accounts->setGroupId($groupId);
        $accounts->setActive($active);
        $accounts->setApiToken(Users::generateToken(16));
        $accounts->setAdminId($adminId);
        $response = $id ? $accounts->update() : $accounts->insert();

        if (!$response) {
            $returnCalls->setMessage(Session::t('An unexpected error occurred'));
        } else {
            if (!$id) {
                $users->setAccountId($response);
                $users->update();

                Log::save(Log::ACCOUNT_ADDED, $response);
            } else {
                Log::save(Log::ACCOUNT_UPDATED, $id, Log::getObjectDifferences($accountsInitial, $accounts));
            }

            $message = $id ? Session::t('Account') . ' [#' . $id . '] ' . Session::t('has been updated.') : Session::t('Account has been created.');
            $returnCalls->setMessage($message);
            $returnCalls->setData($response);
            $returnCalls->setStatusId(ReturnCalls::STATUSID_SUCCESS);
        }
    } elseif (!$adminEmail || !$userName) {
        $returnCalls->setMessage(Session::t('Please enter Admin Email and User Name.'));
    }
}

$returnCalls->getResponse();
