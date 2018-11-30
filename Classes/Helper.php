<?php

class Helper
{
	// Amount of time before window automatically closes
	const CLOSE_TIMER = 10000;
	const TOKENMASKER = '&#9679;';
	// Amount of time to scroll to the error message
	const SCROLL_TIME = 500;
	// The font family used by the helper text
	const FONT_FAMILY = '"Open Sans", "Trebuchet MS", Arial, Helvetica, sans-serif !important';

    public static function printError($message)
    {
        ?>

			var errorId = Date.now();
            $('#alertBox').prepend('<div class="alert alert-danger" id="' + errorId + '" style="display: none;">' +
           			'<button type="button" class="close" aria-hidden="true">&times;</button>' +
           			'<strong>Error!</strong> <?php echo $message; ?>' +
           		'</div>'
           	);

           	$('#' + errorId).fadeIn('slow');

           	// $('html, body').animate({
        	//	scrollTop: $('#' + errorId).offset().top
    		// }, <?php echo Helper::SCROLL_TIME; ?>);

           	$('#' + errorId).on('click', 'button.close', function() {
   				clearTimeout(timeout);
   				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			});

			var timeout = setTimeout(function() {
  				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			}, <?php echo Helper::CLOSE_TIMER; ?>);
			
			$(document).scroll(function() { 
                if ($(document).scrollTop() > 51) {
                    $("#alertBox").addClass("fix-alert");
                    var size = document.getElementById("sidebar-menu").offsetWidth;
                    $("#alertBox").css('width', '100%').css('width', '-=' + size + 'px');
                } else {
                    $("#alertBox").removeClass("fix-alert");
                }              
            });

        <?php
    }

    public static function printWarning($message)
    {
        ?>

          	var errorId = Date.now();
            $('#alertBox').prepend('<div class="alert alert-warning" id="' + errorId + '" style="display: none;">' +
           			'<button type="button" class="close" aria-hidden="true">&times;</button>' +
           			'<strong>Warning!</strong> <?php echo $message; ?>' +
           		'</div>'
           	);

           	$('#' + errorId).fadeIn('slow');

           	$('#' + errorId).on('click', 'button.close', function() {
   				clearTimeout(timeout);
   				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			});

			var timeout = setTimeout(function() {
  				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			}, <?php echo Helper::CLOSE_TIMER; ?>);
			
			$(document).scroll(function() { 
                if ($(document).scrollTop() > 51) {
                    $("#alertBox").addClass("fix-alert");
                    var size = document.getElementById("sidebar-menu").offsetWidth;
                    $("#alertBox").css('width', '100%').css('width', '-=' + size + 'px');
                } else {
                    $("#alertBox").removeClass("fix-alert");
                }              
            });

        <?php
    }

    public static function printenableDisableOptions($id, $value, $name)
    {
        $checked = $value == 1 ? "checked" : "";
        echo '<input ' . $checked . ' id="' . $id . '" name="' . $name . '" data-width="100%" data-height="34" data-toggle="toggle" type="checkbox"/>';
    }

    /**
     * @param $string
     * @return string
     */
    public static function sanitizeString($string)
    {
        $string = strip_tags($string);
        $string = htmlentities($string);
        $string = stripslashes($string);
        $string = trim($string);

        return $string;
    }

    /**
     * Set EU Directive acceptance cookie
     */
    public static function setEuDirectiveCookie()
    {
        setcookie('MT-HUBEuDirective', 1, 2147483647, '/', Session::getSessionDomain(), true, true);
    }

    /**
     * @return bool|null
     */
    public static function getEuDirectiveCookie()
    {
        if (isset($_COOKIE['MT-HUBEuDirective'])) {
            return true;
        }

        return null;
    }

    /**
     * Set default page url
     */
    public static function setDefaultPage()
    {
        switch (Session::getGroupId()) {
            case Groups::GROUP_ADMINISTRATOR:
                $page = 'Pages/ControlPanel/accounts.php';
                break;
            case Groups::GROUP_CONSUMER:
                $page = 'Pages/Consumers/suppliers.php';
                break;
            case Groups::GROUP_SUPPLIER:
                $page = 'Pages/Suppliers/dashboard.php';
                break;
            default:
                $page = 'Pages/Account/settings.php';
        }

        ?>
        <script type="text/javascript">
            window.defaultPage = '<?php echo $page; ?>';
        </script>
        <?php
    }

    /**
     * @param $bytes
     * @param int $precision
     * @param bool $showUnits
     * @return float|int|mixed|string
     */
    public static function formatBytes($bytes, $precision = 1, $showUnits = true)
    {
        $units = [
            'KB',
            'MB',
            'GB',
            'TB'
        ];

        $bytes = max(round($bytes), 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));
        $bytes = round($bytes, $precision);
        $bytes = $showUnits ? $bytes . ' ' . $units[$pow] : $bytes;

        return $bytes;
    }

    public static function printSuccess($message)
    {
        ?>

            var errorId = Date.now();
            $('#alertBox').prepend('<div class="alert alert-success" id="' + errorId + '" style="display: none;">' +
           			'<button type="button" class="close" aria-hidden="true">&times;</button>' +
           			'<?php echo $message; ?>' +
           		'</div>'
           	);

           	$('#' + errorId).fadeIn('slow');

           	// $('html, body').animate({
        	//	scrollTop: $('#' + errorId).offset().top
    		// }, <?php echo Helper::SCROLL_TIME; ?>);

           	$('#' + errorId).on('click', 'button.close', function() {
   				clearTimeout(timeout);
   				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			});

			var timeout = setTimeout(function() {
  				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			}, <?php echo Helper::CLOSE_TIMER; ?>);
			
            $(document).scroll(function() { 
                if ($(document).scrollTop() > 51) {
                    $("#alertBox").addClass("fix-alert");
                    var size = document.getElementById("sidebar-menu").offsetWidth;
                    $("#alertBox").css('width', '100%').css('width', '-=' + size + 'px');
                } else {
                    $("#alertBox").removeClass("fix-alert");
                }              
            });

        <?php
    }

    public static function printInfo($message)
    {
        ?>

            var errorId = Date.now();
            $('#alertBox').prepend('<div class="alert alert-info" id="' + errorId + '" style="display: none;">' +
           			'<button type="button" class="close" aria-hidden="true">&times;</button>' +
           			'<?php echo $message; ?>' +
           		'</div>'
           	);

			$('#' + errorId).fadeIn('slow');

           	// $('html, body').animate({
        	//	scrollTop: $('#' + errorId).offset().top
    		// }, <?php echo Helper::SCROLL_TIME; ?>);

           	$('#' + errorId).on('click', 'button.close', function() {
   				clearTimeout(timeout);
   				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			});

			var timeout = setTimeout(function() {
  				$('#' + errorId).animate({opacity: 0}, 500).hide('slow').queue(function() { $('#' + errorId).remove(); });
			}, <?php echo Helper::CLOSE_TIMER; ?>);
			
			$(document).scroll(function() { 
                if ($(document).scrollTop() > 51) {
                    $("#alertBox").addClass("fix-alert");
                    var size = document.getElementById("sidebar-menu").offsetWidth;
                    $("#alertBox").css('width', '100%').css('width', '-=' + size + 'px');
                } else {
                    $("#alertBox").removeClass("fix-alert");
                }              
            });

        <?php
    }

    /**
     * Add a modal popup for a form
     *
     * @param string Button ID
     * @param string Form URL
     * @param string Form title
     */
    public static function printModal($elementId, $contentUrl, $title, $content = false)
    {
        $randomStr = time() . rand(0,10000);

        // If we display in the modal content, lets add to it a close button.
        $contentButton = '<div class="modal-footer"><button type="button" class="btn btn-info" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>&nbsp&nbsp;Close</button></div>';
        ?>
        
        <style>
        
            .modal-dialog {
                font-family: <?php echo Helper::FONT_FAMILY; ?>;
            }
            
        </style>

        <!-- Modal -->
      	<div class="modal fade" id="modal<?php echo $randomStr; ?>" role="dialog" aria-labelledby="modalLabel<?php echo $randomStr; ?>">
        	<div class="modal-dialog" role="document">
          		<div class="modal-content">
            		<div class="modal-header modal-header-primary">
              			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              			<h4 class="modal-title" id="modalLabel<?php echo $randomStr; ?>"><?php echo $title; ?></h4>
            		</div>
                    <div class="modal-body">      
                        <div id="modalContent<?php echo $randomStr; ?>"></div>	
                    </div>
            		<div id="modalFooter<?php echo $randomStr; ?>"></div>	
          		</div>
        	</div>
      	</div>

        <script type="text/javascript">

			$(function() {
           		$('#<?php echo $elementId; ?>').click( function(){
            		$('#modal<?php echo $randomStr; ?>').modal();
            		<?php if ($content) { ?>
            		$('#modalContent<?php echo $randomStr; ?>').html('<?php echo $content; ?>');
            		<?php } else { ?>
            		$('#modalContent<?php echo $randomStr; ?>').html(loader).load('<?php echo $contentUrl; ?>');
            		<?php } ?>
            		$('#modalFooter<?php echo $randomStr; ?>').html('<?php echo $contentButton; ?>');
              	});
            });

			$('.modal').on('hide.bs.modal', function(e) {    
			    var $frame = $(e.delegateTarget).find('iframe');
			    $frame.attr("src", '');
			});

        </script>

        <?php
    }
    
    public static function makeScrollable($element)
    {
        ?>
        
        <script type="text/javascript">

            $(function() {
            	$("<?php echo $element; ?>").mCustomScrollbar({theme:'dark-thick', advanced:{updateOnContentResize: true}});
            });
            
        </script>

        <?php 
    }

    /**
     * @param $title
     * @param $content
     * @param null $placement
     * @param null $icon
     */
    public static function printPopoverButton(
            $title,
            $content,
            $placement = null,
            $icon = null
    ) {
        $placement =  $placement ? $placement : 'auto';
        $icon = $icon ? $icon : 'fa-info-circle';
        ?>
        	<span
                class = "popoverButton"
                type = "button"
                data-toggle = "popover"
                data-trigger = "hover"
                data-container="body"
                title= "<?php echo $title; ?>"
                data-content = "<?php echo $content; ?>"
                data-placement = "<?php echo $placement; ?>">
                <sup><i class="fa <?php echo $icon; ?> fa-pointer fa-deeporange"></i></sup>
            </span>

       		<script type="text/javascript">
               	 $('[data-toggle="popover"]').popover({
                 	html: true
                 });
            </script>

    	<?php
    }

    public static function printYouTubeButton($title, $youTubeId)
    {
        echo '<span id="' . $youTubeId . '" title="Watch video help" class="fa-stack fa-pointer fa-lg margin-top--10">' .
            '<i class="fa fa-circle fa-deeporange fa-pointer fa-stack-2x"></i>' .
            '<i class="fa fa-video-camera fa-stack-1x fa-inverse"></i>' .
        '</span>';

        $youTube = '<iframe width="660" height="360" src="https://www.youtube-nocookie.com/embed/' . $youTubeId . '?rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>';

        self::printModal($youTubeId, null, $title, $youTube);
    }

    public static function printHelpButton($title, $content, $url = null)
    {
        $randomStr = time() . rand(0,10000);
        
        if ($url) { ?>
        	<a href="<?php echo $url; ?>" target="_blank">
       	<?php } ?>
  
       	<span id="<?php echo $randomStr; ?>" title="Help" class="fa-stack fa-pointer fa-lg margin-top--10">
            <i class="fa fa-circle fa-deeporange fa-pointer fa-stack-2x"></i>
            <i class="fa fa-question fa-stack-1x fa-inverse"></i>
        </span>
	
		<?php 
		
		if ($url) { ?>
			</a>
		<?php } else { 
            self::printModal($randomStr, $content, $title, null);
		}
    }

    public static function maskToken($string)
    {
        if (strlen($string) >= 5) {
            return str_repeat(Helper::TOKENMASKER, strlen($string) - 4) . substr($string, strlen($string) - 4);
        } else {
            return $string;
        }
    }

    public static function getMySqlCurrentTime()
    {
        return date("Y-m-d H:i:s", time());
    }
    
    public static function storeBreadCrumb($breadCrumb)
    {
        $_SESSION['breadcrumb'] = $breadCrumb;    
    }

    /**
     * Format plan type feature status
     *
     * @param $status
     * @return string
     */
    public static function formatFeatureStatus($status) {
        if ($status == 1) {
            return '<i class=\'fa fa-lg fa-check fa-green\' aria-hidden=\'true\' title=\'Enabled\'></i>';
        } else if ($status == -1){
            return 'Unlimited';
        } else if ($status == 0){
            return '<i class=\'fa fa-lg fa-ban fa-red\' aria-hidden=\'true\' title=\'Disabled\'></i>';
        } else {
            return $status;
        }
    }

    public static function getBreadCrumb()
    {
        return isset($_SESSION['breadcrumb']) ? $_SESSION['breadcrumb'] : null;
    }
    
    /**
     * Display content header section. The section includes:<br/>
     * - the page title<br/>
     * - bradcrumbs
     * 
     * @param array
     * <ul>
     *  <li>
     *   <strong>title</strong>* string - Page title display in the left top corner.
     *  </li>
     *  <li>
     *   <strong>description</strong> string - Page description. It was decided that we will not use the description on the app, therefore this option will be not dispayed.
     *  </li>
     *  <li>
     *   <strong>breadcrumbs</strong> array - Defines breadcrumbs elements
     *      <ul>
     *        <li><strong>pagename</strong> <i><u>string</u></i> Defines breadcrumb name</li>
     *        <li><strong>icon</strong> <i><u>string</u></i> Defines breadcrumb icon.</li>
     *        <li><strong>active</strong> <i><u>boolean</u></i> If set to true will add the 'active' class to breadcrumb.</li>
     *        <li><strong>link</strong> <i><u>string</u></i> Defines breadcrumb link.</li>
     *      </ul>
     *  </li>
     * </ul>
     */
    public static function displayPageHeader($elements) 
    {
        $title = isset($elements['title']) ? $elements['title'] : '';
        $description = isset($elements['description']) ? $elements['description'] : false;
        $breadCrumbs = isset($elements['breadcrumbs']) ? $elements['breadcrumbs'] : false; 
        $popover = isset($elements['popover']) ? $elements['popover'] : false;
        $i = 0;
        $len = count($breadCrumbs);
        ?>
        
        <section class="content-header">
            <h1 class="box-title width-60"><?php echo $title; ?>
            <span class="font-small">
            <?php if ($popover) {
            	self::printPopoverButton($title, $popover);
            }?>
            </span>
            </h1>
            <?php if ($breadCrumbs) { ?>
            <ol class="breadcrumb">
            	<?php 
            	   foreach ($breadCrumbs as $breadCrumb) { 
            	       $link = isset($breadCrumb['link']) ? $breadCrumb['link'] : false;
            	       // Make sure we dont link the last element of the breadcrumb
            	       $link = $i == $len - 1 ? false : $link;
            	       $pageName = isset($breadCrumb['pagename']) ? $breadCrumb['pagename'] : false;
            	       $icon = isset($breadCrumb['icon']) ? $breadCrumb['icon'] : false;
            	       // Check if this is the last element of breadcrum and it is display the home icon
            	       $icon = $i == $len - 1 ? 'fa-home fa-margin-right-5' : $icon;
            	       $active = isset($breadCrumb['active']) ? 'active' : '';
            	       
            	       if ($pageName) {
                	       echo '<li class="breadcrumb-item ' . $active . '">';
                	       echo $link ? '<a href="' . $link . '">' : '';
                	       echo $icon ? '<i class="fa ' . $icon. '"></i> ' : '';
                	       echo $pageName;
                	       echo $link ? '</a>' : '';
                	       echo '</li>';
            	       }
            	       
            	       $i++;
            	   } 
                ?>
            </ol>
            <?php } ?>
            <?php if ($description) { ?>
            <p>
            	<?php echo $description; ?>
            </p>
            <?php } ?>
        </section>
        
        <?php 
    }
    
    /**
     * Display engine name
     * 
     * @param string $ngineName
     * @param string $src
     * @param string $trg
     */
    public static function displayEngineName($ngineName, $src = null, $trg = null)
    {
        $src = empty($src) ? '' : $src;
        $trg = empty($trg) ? '' : $trg;
        $engineName = empty($ngineName) ? 'None' : trim($ngineName);

        ?>

        <h3 class="box-title">
            Active Engine [
            <i>
                <span class="activeEngineName"><?php echo $engineName; ?></span>
                <span class="engineLanguagePairs" style="margin-left: 10px;">
                    <span id="activeEngineSrcFlag" class="font-xsmall flag-icon flag-icon-<?php echo $src; ?>"></span>
                    <span class="activeEngineSrc"><?php echo $src; ?></span>
                    =>
                    <span id="activeEngineTrgFlag" class="font-xsmall flag-icon flag-icon-<?php echo $trg; ?>"></span>
                    <span class="activeEngineTrg"><?php echo $trg; ?></span>
                </span>
            </i>
            ]
        </h3>

        <?php
    }

    /**
     * @param $name
     */
    public static function displaySupplier($name)
    {
        $name = empty($name) ? 'None' : trim($name);

        ?>

        <h3 class="box-title">
            Active Supplier [
            <i>
                <span class="activeSupplierName"><?php echo $name; ?></span>
            </i>
            ]
        </h3>

        <?php
    }

    /**
     * Display engine name
     *
     * @param string $ngineName
     * @param string $src
     * @param string $trg
     */
    public static function displayActiveDomainName($name, $src = null)
    {
        $src = empty($src) ? '' : $src;
        $name = empty($name) ? 'None' : trim($name);

        ?>

        <h3 class="box-title">
            Active Domain [
            <i>
                <span class="activeDomainName"><?php echo $name; ?></span>
                <span class="engineLanguagePairs" style="margin-left: 10px;">
                    <span id="activeDomainSrcFlag" class="font-xsmall flag-icon flag-icon-<?php echo $src; ?>"></span>
                    <span class="activeDomainSrc"><?php echo $src; ?></span>
                </span>
            </i>
            ]
        </h3>

        <?php
    }

    public static function getAliasTag($alias) {
        if ( ! empty($alias)) {
            return "<span class=\"alias-tag\" title=\"Alias Group Name\">" . $alias . "</span>";
        }
        return '';
    }


    public static function removeDirectory($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }

            rmdir($dir);
        }
    }

    public static function displayHelp($text, $url = null)
    {
        ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="insight font-lsmall">
                        <i class="fa fa-lightbulb-o fa-3x"></i>
                        <span>

                            <?php

                            echo $text;

                            if ($url) {

                            ?>

                                <a class="wizard-info-link" href="<?php echo $url; ?>" target="_blank">click here.</a>

                            <?php } ?>
                            
                        </span>
                    </div>
                </div>
            </div>

        <?php
    }

    /**
     * Removes all special characters
     *
     * @param string $string
     */
    public static function removeSpecialCharacters($string)
    {
        $special_chars = ["/", "\\", "=", "<", ">", "≥", ";", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}"];
        $string = str_replace($special_chars, '', $string);

        return $string;
    }
}
