<aside class="main-sidebar">
	<?php if (Session::getLoginStatus()) { ?>
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul id="sidebar-menu" class="sidebar-menu">
            <?php if (in_array(Session::getGroupId(), [Groups::GROUP_SUPPLIER])) { ?>
            <li class="header" id="suppliers-menu">
            	<?php echo Session::t('Suppliers'); ?>
            </li>
            <li class="suppliers-menu">
                <a href="#supdashboard">
                    <i class="fa fa-dashboard fa-fw"></i>
                    <span><?php echo Session::t('Dashboard'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#enginereports">
                    <i class="fa fa-th fa-fw"></i>
                    <span><?php echo Session::t('Engine Reports'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#consumers">
                    <i class="fa fa-handshake-o fa-fw"></i>
                    <span><?php echo Session::t('Consumer'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#domains">
                    <i class="fa fa-cubes fa-fw"></i>
                    <span><?php echo Session::t('Domains'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#statistics">
                    <i class="fa fa-pie-chart fa-fw"></i>
                    <span><?php echo Session::t('Statistics'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#testzone">
                    <i class="fa fa-connectdevelop fa-fw"></i>
                    <span><?php echo Session::t('Test Zone'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#supplierlogs">
                    <i class="fa fa-list-ol fa-fw"></i>
                    <span><?php echo Session::t('Activity Log'); ?></span>
                </a>
            </li>
            <?php } ?>
            <?php if (in_array(Session::getGroupId(), [Groups::GROUP_CONSUMER])) { ?>
            <li class="header" id="consumers-menu">
            	<?php echo Session::t('Consumers'); ?>
            </li>
            <li class="consumers-menu">
                <a href="#suppliers">
                    <i class="fa fa-qrcode fa-fw"></i>
                    <span><?php echo Session::t('Suppliers'); ?></span>
                </a>
            </li>
            <li class="consumers-menu">
                <a href="#availableengines">
                    <i class="fa fa-th fa-fw"></i>
                    <span><?php echo Session::t('Engines'); ?></span>
                </a>
            </li>
            <li class="suppliers-menu">
                <a href="#translatebox">
                    <i class="fa fa-language fa-fw"></i>
                    <span><?php echo Session::t('Translate Box'); ?></span>
                </a>
            </li>
            <li class="consumers-menu">
                <a href="#consumerstatistics">
                    <i class="fa fa-pie-chart fa-fw"></i>
                    <span><?php echo Session::t('Statistics'); ?></span>
                </a>
            </li>
            <?php } ?>
            <?php if (in_array(Session::getGroupId(), [Groups::GROUP_ADMINISTRATOR])) { ?>
                <li class="header" id="controlPanel-menu">
                    <?php echo Session::t('Control Panel'); ?>
                </li>
                <li class="controlPanel-menu">
                    <a href="#accounts">
                        <i class="fa fa-address-card fa-fw"></i>
                        <span><?php echo Session::t('Accounts'); ?></span>
                    </a>
                </li>
                <li class="controlPanel-menu">
                    <a href="#engines">
                        <i class="fa fa-th-list fa-fw"></i>
                        <span><?php echo Session::t('Engines'); ?></span>
                    </a>
                </li>
                <li class="controlPanel-menu">
                    <a href="#requestlogs">
                        <i class="fa fa-list-ul fa-fw"></i>
                        <span><?php echo Session::t('Request Log'); ?></span>
                    </a>
                </li>
                <li class="controlPanel-menu">
                    <a href="#logs">
                        <i class="fa fa-list-ol fa-fw"></i>
                        <span><?php echo Session::t('Activity Log'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <li class="header" id="settings-menu">
                <?php echo Session::t('Account'); ?>
            </li>
            <li class="settings-menu">
                <a href="#settings">
                    <i class="fa fa-user-circle-o fa-fw"></i>
                    <span><?php echo Session::t('Settings'); ?></span>
                </a>
            </li>
            <?php if (in_array(Session::getGroupId(), [Groups::GROUP_SUPPLIER])) { ?>
                <li class="settings-menu">
                    <a href="#apiconfiguration">
                        <i class="fa fa-cog fa-fw"></i>
                        <span><?php echo Session::t('API Configuration'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <li class="header" id="mt-hub-menu">
                <?php echo Session::t('MT-Hub'); ?>
            </li>
            <li class="mt-hub-menu">
                <a href="#about">
                    <i class="fa fa-qrcode fa-fw"></i>
                    <span><?php echo Session::t('About'); ?></span>
                </a>
            </li>
            <li class="mt-hub-menu">
                <a href="#connectors">
                    <i class="fa fa-handshake-o fa-fw"></i>
                    <span><?php echo Session::t('Connectors'); ?></span>
                </a>
            </li>
            <li class="footer">
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
    <?php } ?>
</aside>
