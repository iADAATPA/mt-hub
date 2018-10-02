<?php

include_once 'functions.php';

?>

<!DOCTYPE html>
<html>
	<head>
    	<?php include_once "Layout/head.php"; ?>
    </head>
	<body class="hold-transition sidebar-mini">
		<div class="wrapper">

          	<header class="main-header">
                <!-- Logo -->
                <a href="#" class="logo">
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
          	</header>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper content-wrapper-login">
              <div id="alertBox"></div>
                <div class="container">
            		<div class="row row-xs-offset-1">
                		<div class="col-md-6 col-md-offset-3 text-center">
                		<h1 style="font-size: 12em;">404</h1>
                		<h1><?php echo Session::t('Page not found!'); ?></h1>
                		<h1>
                			<div class="btn-group">
                				<a href="index.php" target="_self"><button type="button" class="btn btn-lg btn-info" title="Go to the home page"><i class="fa fa-home fa-pointer fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Home'); ?></button></a>
                				<a href="mailto:info@iadaatpa.eu?Subject=Page%20Not%20Found%20Error target="_self"><button type="button" class="btn btn-lg btn-info" title="Contact support"><i class="fa fa-home fa-envelope fa-fw fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo Session::t('Contact us'); ?></button></a>
                			</div>
                		</h1>
                		</div>
                	</div>
                </div>
            </div>

			<?php include_once "Layout/footer.php"; ?>

  		</div>

	<?php include_once "Layout/scripts.php"; ?>

	</body>

</html>
