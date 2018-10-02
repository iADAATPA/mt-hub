<?php

include_once '../../functions.php';

Session::authenticateUser();

$breadCrumb =  [
    'icon' => 'fa-cog',
    'link' => 'Pages/Account/apiconfiguration.php',
    'pagename' => Session::t('API Configuration')
];

Helper::storeBreadCrumb($breadCrumb);

$header = [
    'title' => Session::t('API Configuration'),
    'breadcrumbs' => [
       $breadCrumb
    ]
];

Helper::displayPageHeader($header);

?>


<section class="content">
    <div class="row">
        <div class"col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="dashboardTabs">
                <li class="active">
                    <a href="#translate" data-toggle="tab"><?php echo Session::t(UrlConfig::METHOD_TRANSLATE_DESC); ?></a>
                </li>
                <li>
                    <a href="#aTranslate" data-toggle="tab"><?php echo Session::t(UrlConfig::METHOD_ATRANSLATE_DESC); ?></a>
                </li>
                <li>
                    <a href="#translateFile" data-toggle="tab"><?php echo Session::t(UrlConfig::METHOD_TRANSLATE_FILE_DESC); ?></a>
                </li>
                <li>
                    <a href="#aTranslateFile" data-toggle="tab"><?php echo Session::t(UrlConfig::METHOD_ATRANSLATE_FILE_DESC); ?></a>
                </li>
                <li>
                    <a href="#retrieveFileTranslation" data-toggle="tab"><?php echo Session::t(UrlConfig::METHOD_RETRIEVE_FILE_TRANSLATION_DESC); ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="translate" class="tab-pane fade in active"></div>
                <div id="aTranslate" class="tab-pane fade"></div>
                <div id="translateFile" class="tab-pane fade"></div>
                <div id="aTranslateFile" class="tab-pane fade"></div>
                <div id="retrieveFileTranslation" class="tab-pane fade"></div>
            </div>
        </div>
    </div>
    </div>
</section>

<script type="text/javascript">
    $(function() {
        var link = 'Pages/Account/Forms/apiconfiguration.php?methodId=<?php echo UrlConfig::METHOD_TRANSLATE_ID; ?>';
        $("#translate").html(loader).load(link);

        $('[href="#translate"]').click(function() {
            $("#translate").html(loader).load(link);
        });

        $('[href="#translateFile"]').click(function(e) {
            if ($('[href="#translateFile"]').closest('li').hasClass("disabled")) {
                e.preventDefault();

                return false;
            } else {
                var link = 'Pages/Account/Forms/apiconfiguration.php?methodId=<?php echo UrlConfig::METHOD_TRANSLATE_FILE_ID; ?>';
                $("#translateFile").html(loader).load(link);
            }
        });

        $('[href="#retrieveFileTranslation"]').click(function(e) {
            if ($('[href="#retrieveFileTranslation"]').closest('li').hasClass("disabled")) {
                e.preventDefault();

                return false;
            } else {
                var link = 'Pages/Account/Forms/apiconfiguration.php?methodId=<?php echo UrlConfig::METHOD_RETRIEVE_FILE_TRANSLATION_ID; ?>';
                $("#retrieveFileTranslation").html(loader).load(link);
            }
        });

        $('[href="#aTranslate"]').click(function(e) {
            if ($('[href="#aTranslate"]').closest('li').hasClass("disabled")) {
                e.preventDefault();

                return false;
            } else {
                var link = 'Pages/Account/Forms/apiconfiguration.php?methodId=<?php echo UrlConfig::METHOD_ATRANSLATE_ID; ?>';
                $("#aTranslate").html(loader).load(link);
            }
        });

        $('[href="#aTranslateFile"]').click(function(e) {
            if ($('[href="#aTranslateFile"]').closest('li').hasClass("disabled")) {
                e.preventDefault();

                return false;
            } else {
                var link = 'Pages/Account/Forms/apiconfiguration.php?methodId=<?php echo UrlConfig::METHOD_ATRANSLATE_FILE_ID; ?>';
                $("#aTranslateFile").html(loader).load(link);
            }
        });
    });

</script>
