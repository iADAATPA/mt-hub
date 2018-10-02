<?php

if (Session::getLoginStatus()) {
    $userName = Session::getUserName();
    $image = Session::getUserPhoto();

    // Crop the username to be displayed in the header
    // First try crop at the '@' in the email address
    // Crop length
    $length = 32;
    if (strlen($userName) > $length) {
        $pos = strpos($userName, '@');
        $userName = substr($userName, 0, $pos);
    }
    // If the string is still too long crop it and append with '...'
    if (strlen($userName) > $length) {
        $subString = substr($userName, 0, $length-3);
        $userName = $subString . '...';
    }

?>
<!-- Logo -->
<a href="index.php" class="logo">
  <!-- mini logo for sidebar mini 50x50 pixels -->
  <span class="logo-mini">
  <img src="Images/Logo/icon.png" />
  </span>
  <!-- logo for regular state and mobile devices -->
  <span class="logo-lg">
  <img src="Images/Logo/logo.png" />
  </span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">

  <!-- Sidebar toggle button-->
  <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only"><?php echo Session::t('Toggle navigation'); ?></span>
  </a>

  <div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
      <li class="user-options">
        <a href="#" onclick="goToPage('Pages/Account/settings.php')">
          <img id="headerProfileImage" src="<?php echo $image; ?>" class="user-image" alt="">
          <span id="headerProfileName" class="profile"><?php echo $userName; ?></span>
        </a>
      </li>
      <li>
        <button id="logout" type="submit" class="btn btn-info">
          <span id="logoutLoading"><i class="fa fa-sign-out fa-fw" aria-hidden="true"></i></span>&nbsp;&nbsp;<?php echo Session::t('Logout'); ?>
        </button>
      </li>
    </ul>
  </div>
</nav>
<?php } else { ?>
<!-- Logo -->
<a href="index.php" class="logo">
  <!-- mini logo for sidebar mini 50x50 pixels -->
  <span class="logo-mini">
  <img src="Images/Logo/icon.png" />
  </span>
  <!-- logo for regular state and mobile devices -->
  <span class="logo-lg">
  <img src="Images/Logo/logo.png" />
  </span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">

</nav>
<?php }
