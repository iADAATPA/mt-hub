<?php

include_once '../functions.php';

$name = empty($_POST['name']) ? null : $_POST['name'];
$returnCalls = new ReturnCalls();
$returnCalls->setMessage(Session::t('For security reasons, we cannot retrieve your original password. However, we just sent you a link where you can reset your password.'));

if ($name) {
    $users = new Users(null, $name);

    if ($users->getId()) {
        // check whether the user is trying to request a new password after being locked out - they can't!
        $loginAttempts = $users->getLoginAttempts();

        if ($loginAttempts < 5) {
            $username = $users->getName();
            // because we hash the passwords, we cannot send them back their original password, so
            // instead we email them with a link to a page (setpassword.php) which allows them to set a new one
            $token = $users->generateToken();
            $users->setToken($token);
            $users->update();

            // Log the event
            Log::save(Log::USER_UPDATED, $users->getId());

            // Send an email with the link
            $mail = new Mail();
            // define subject
            $subject = Session::t('Please reset your password');
            $mail->sendHtmlEmail(
                $users->getEmail(),
                $subject,
                Session::t('Hi') . ' ' . $users->getName() . '<br/><br/>' . Session::t('Please set up your password to the MT-HUB platform by clicking on the following link:') . ' <a href="https://mt-hub.eu?token=' . $token . '">www.mt-hub.eu</a>.<br/><br/>' . Session::t('Your User Name is:') . ' ' . $users->getName() . '<br/><br/><br/><br/>MT-HUB Team'
            );


        } else {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('The account has been locked out due to too many incorrect login attempts.<br>Please contact our support.'));
        }
    }
} else {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Missing or incorrect User Name.'));
}

// Return the error status
$returnCalls->getResponse();
