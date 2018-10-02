<?php

include_once '../functions.php';

$name = empty($_POST['name']) ? null : $_POST['name'];
$password = empty($_POST['password']) ? null : Helper::sanitizeString($_POST['password']);
$returnCalls = new ReturnCalls();

if ($name) {
    // Search for login details
    $users = new Users(null, $name);

    if (!$users->getId()) {
        $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
        $returnCalls->setMessage(Session::t('Invalid User Name.'));
    } else {
        // Get failed login number
        $failedLogin = $users->getLoginAttempts();

        // Get account details
        $accounts = new Accounts($users->getAccountId());

        if (!$accounts->getActive()) {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Inactive Account.'));
        } elseif ($users->getLoginAttempts() < 5) {
            if (!$users->verifyPassword($password)) {
                $failedLogin++;
                $users->setLoginAttempts($failedLogin);
                $users->update();

                $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
                // If the user entered the wrong passwrod 3 times show him a warning that his account will be locked
                if ($failedLogin == 3) {
                    $returnCalls->setMessage(Session::t('If you enter the wrong password two more times your account will be locked.'));
                } else {
                    $returnCalls->setMessage(Session::t('Invalid Password.'));
                }
            } else {
                Session::setGroupID($accounts->getGroupId());
                Session::setUserName($users->getName());
                Session::setUserId($users->getId());
                Session::setUserPhoto($users->getPhoto());
                Session::setUserEmail($users->getEmail());
                Session::setAdmin($users->getId() == $accounts->getAdminId() ? true : false);
                Session::setAccountID($users->getAccountId());
                // Set eu cookies
                Helper::setEuDirectiveCookie();

                $activeEngineId = null;
                $engines = new Engines();
                $engines->setAccountId($users->getAccountId());
                $details = $engines->getAllAccountEngines();

                if (is_array($details) && count($details) > 0) {
                    reset($details);
                    $key = key($details);
                    $details = $details[$key];
                }

                $engines->set($details);
                Session::setActiveEngineId($engines->getId());

                // Date stamp user lastlogin field
                $users->setLastlogin(Helper::getMySqlCurrentTime());
                $users->setLoginAttempts(0);
                $users->setToken(null);
                $users->update();

                // Log the event
                Log::save(Log::USER_LOGIN);
            }
        } else {
            $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
            $returnCalls->setMessage(Session::t('Your account has been locked out due to too many incorrect login attempts. Please contact support.'));
        }
    }
} else {
    $returnCalls->setStatusId(ReturnCalls::STATUSID_ERROR);
    $returnCalls->setMessage(Session::t('Missing or incorrect User Name.'));
}

// Return the error status
$returnCalls->getResponse();
