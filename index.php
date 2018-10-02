<?php

include_once 'functions.php';

Session::setLang();
$userId = Session::getUserId();
$users = new Users($userId);
Session::setUserName($users->getName());

?>

<!DOCTYPE html>
<html>

<head>
    <?php include_once 'Layout/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <?php include_once 'Layout/header.php'; ?>
        </header>

        <?php if (Session::getLoginStatus()) { ?>
            <?php Helper::setDefaultPage(); ?>
            <?php include_once 'Layout/menu.php'; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div id="alertBox"></div>
                <div id="pageContent"></div>
            </div>

        <?php } else { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper content-wrapper-login">
                <div id="alertBox"></div>
                <?php include_once 'Pages/login.php'; ?>
            </div>

        <?php } ?>

        <?php include_once 'Layout/footer.php'; ?>

    </div>
    <?php include_once 'Layout/scripts.php'; ?>
</body>

</html>
