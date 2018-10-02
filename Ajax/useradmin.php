<?php

include_once '../functions.php';

Session::authenticateUser();
Csrf::validateToken();
$returnCalls = new ReturnCalls();
$returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
$returnCalls->setMessage(Session::t('An unexpected error occurred'));

$id = empty($_POST['id']) ? null : $_POST['id'];
$users = new Users($id);
$usersInitial = clone($users);
$accountId = empty($_POST['accountId']) ? $users->getAccountId() : $_POST['accountId'];
$name = empty($_POST['name']) ? $users->getName() : $_POST['name'];
$email = empty($_POST['email']) ? $users->getEmail() : $_POST['email'];
$loginAttempts = empty($_POST['loginAttempts']) ? null : $_POST['loginAttempts'];
$loginAttempts = $loginAttempts >= 0 && $loginAttempts < 6 ? $loginAttempts : $users->getLoginAttempts();

$userValidation = new Users(null, $name);

if ($userValidation->getAccountId() && ($users->getAccountId() != $userValidation->getAccountId())) {
    $returnCalls->setMessage(Session::t('Please choose another User Name.'));
} elseif (!$email || !Mail::isValidEmail($email)) {
    $returnCalls->setMessage(Session::t('Please enter valid Email.'));
} elseif (empty($name)) {
    $returnCalls->setMessage(Session::t('Please enter User Name.'));
} else {
    $users->setAccountId($accountId);
    $users->setName($name);
    $users->setEmail($email);
    $users->setLoginAttempts($loginAttempts);

    if ($id) {
        $response = $users->update();
    } else {
        $token = Users::generateToken();
        $users->setToken($token);
        $response = $users->insert();

        if ($response) {
            $mail = new Mail();
            $mail->sendHtmlEmail(
                $email,
                Session::t('New User created.'),
                Session::t('Hi') . ' ' . $name . '<br/><br/>' . Session::t('Please set up your password to the IADAATPA platform by clicking on the following link:') . ' <a href="https://iadaatpa.eu?token=' . $token . '">www.iadaatpa.eu</a>.<br/><br/>' . Session::t('Your User Name is:') . ' ' . $name . '<br/><br/><br/><br/>IADAATPA Team'
            );
        }
    }

    if ($response) {
        Log::save($id ? Log::USER_UPDATED : Log::USER_ADDED, $id ? $id : $response,
            $id ? Log::getObjectDifferences($usersInitial, $users) : null);

        $message = $id ? Session::t('User') . ' [#' . $id . '] ' . Session::t('has been updated.') : Session::t('User has been created.');
        $returnCalls->setMessage($message);
        $returnCalls->setStatusId(ReturnCalls::STATUSID_SUCCESS);
    }
}

$returnCalls->getResponse();
