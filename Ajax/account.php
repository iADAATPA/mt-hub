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
$cache = empty($_POST['cache']) ? false : true;
$activiaTm = empty($_POST['activiaTm']) ? false : true;
$activiaTmUserName = empty($_POST['activiaTmUserName']) ? $accounts->getActiviaTmUserName() : $_POST['activiaTmUserName'];
$activiaTmPassword = empty($_POST['activiaTmPassword']) ? $accounts->getActiviaTmPassword() : $_POST['activiaTmPassword'];

if ($activiaTm && !empty($activiaTmUserName) && !empty($activiaTmPassword)) {
    $token = Encryption::generateKey();
    $activiaTmPassword = Encryption::encrypt($activiaTmPassword, $token);
} else {
    $activiaTmPassword = null;
    $activiaTmUserName = null;
    $token = null;
}

$active = empty($_POST['active']) ? false : true;
$groupId = empty($_POST['groupid']) ? $accounts->getGroupId() : $_POST['groupid'];
$name = empty($_POST['name']) ? $accounts->getName() : $_POST['name'];
$adminEmail = empty($_POST['adminEmail']) ? null : $_POST['adminEmail'];
$userName = empty($_POST['userName']) ? null : $_POST['userName'];

if ($activiaTm && empty($activiaTmPassword) && empty($activiaTmUserName)) {
    $returnCalls->setMessage(Session::t('Please enter a ActiviaTM Password and User Name'));
} elseif ($adminEmail && !Mail::isValidEmail($adminEmail)) {
    $returnCalls->setMessage(Session::t('Please enter a valid Admin Email.'));
} elseif (empty($name)) {
    $returnCalls->setMessage(Session::t('Please enter an account Name.'));
} else {
    if ($adminEmail && $userName) {
        $users = new Users(null, $userName);

        if (!$users->getAccountId()) {
            $users->setEmail($adminEmail);
            $users->setName($userName);
            $userToken = Users::generateToken();
            $users->setToken($userToken);
            $adminId = $users->insert();

            if ($adminId) {
                Log::save(Log::USER_ADDED, $adminId);

                $users->setId($adminId);
                $users->update();

                $mail = new Mail();
                $mail->sendHtmlEmail(
                    $adminEmail,
                    Session::t('New Account created.'),
                    Session::t('Hi') . ' ' . $userName . '<br/><br/>' . Session::t('Please set up your password to the MT-HUB platform by clicking on the following link:') . ' <a href="https://mt-hub.eu?token=' .  $userToken . '">www.mt-hub.eu</a>.<br/><br/>' . Session::t('Your User Name is:') . ' ' . $userName . '<br/><br/><br/><br/>MT-HUB Team'
                );
            }
        } else {
            $returnCalls->setMessage(Session::t('Please choose another User Name.'));
        }
    }

    if ($adminId) {
        $accounts->setName($name);
        $accounts->setGroupId($groupId);
        $accounts->setCache($cache);
        $accounts->setActiviaTm($activiaTm);
        $accounts->setActiviaTmUserName($activiaTmUserName);
        $accounts->setActiviaTmPassword($activiaTmPassword);
        $accounts->setToken($token);
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

            $message = $id ? Session::t('Account has been updated.') : Session::t('Account has been created.');
            $returnCalls->setMessage($message);
            $returnCalls->setData($response);
            $returnCalls->setStatusId(ReturnCalls::STATUSID_SUCCESS);
        }
    } elseif (!$adminEmail || !$userName) {
        $returnCalls->setMessage(Session::t('Please enter Admin Email and User Name.'));
    }
}

$returnCalls->getResponse();
