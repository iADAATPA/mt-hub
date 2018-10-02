$(function() {
	 //setup ajax error handling
    $.ajaxSetup({
        error: function (x, status, error) {
            if (x.status == 403) {
            	logoutUserWithMessage("Sorry, your session has expired. Please login again to continue.");
            } else if (x.status == 520) {
            	window.location.href = '520.php';
            } else if (x.status == 500) {
            	window.location.href = '520.php';
            }
        }
    });

    // Default loader
    window.loader = '<div class="loading-center" ><div class="loading-vcenter"><i class="fa fa-2x fa-refresh fa-spin fa-fw"></i></div></div>';
    // Get page url and convert it to aray
    var url = window.location.href.split('/');
    var urlLastElement = url.pop();
    var page = getPageUrl(urlLastElement);
    goToPage(page);

    // Sidebar menu elements
    $('#sidebar-menu li a').not('.multilevel-parent').click(function() {
        try {
            window.stop();
        } catch (exception) {
            document.execCommand('Stop');
        }

        // Check if datatable refresh interval is set and if it is clear it
        if (typeof interval !== 'undefined') {
        	clearInterval(interval);
        }

        removeActiveMenuElements();
        var menuElement = $(this);

        addActiveMenuElement(menuElement);

        var fontClass = menuElement.find('i').attr("class");
        menuElement.find('i').toggleClass(fontClass + ' fa fa-refresh fa-spin fa-fw');

        var page = menuElement.attr('href');
        page = getPageUrl(page);
        loadPageContent(page, menuElement, fontClass);

        return false;
    });

    $('.sidebar-menu .header').click(function(){
    	// Get the id. This will be used to hide all menu alements with a class name like the id
    	var menuId =  $(this).attr('id');
    	// Check if element hidden. We will use it to store in a cookies the menu status
    	var visibility = $('.' + menuId).is(":visible");

    	// Hide or show a menu elemnts wiht a class name like the header id
    	$('.' + menuId).toggle('slow', function() {
            // After the sidebar has changed make sure the footer goes to the bottom of the page too
            var mainSidebarHeight = $('.main-sidebar').height();
            var contentWrapperHeight = $('.content-wrapper').height();
            var footerHeight = 54;

            if (mainSidebarHeight-footerHeight > contentWrapperHeight) {
                $('.content-wrapper').css('min-height', mainSidebarHeight + 'px');
            }
        });

    	// Set the menu status in a cookie.
    	if(!visibility) {
    		createCookie(menuId, true);
    	} else {
    		createCookie(menuId, false);
    	}

    	// Change the header icon
    	$(this).find('i').toggleClass('fa-angle-right fa-angle-down');

    });

    // Check a cookies for side menu status and set the menu
    var suppliersMenu = readCookie('suppliers-menu');
    toggleMenuStatus(suppliersMenu, 'suppliers-menu');
    var consumersMenu = readCookie('consumers-menu');
    toggleMenuStatus(consumersMenu, 'consumers-menu');
    var controlPanelMenu = readCookie('controlPanel-menu');
    toggleMenuStatus(controlPanelMenu, 'controlPanel-menu');
    var settingsMenu = readCookie('settings-menu');
    toggleMenuStatus(settingsMenu, 'settings-menu');

    // Logout the user and refresh the page
    $('#logout').click( function() {
        $('#logoutLoading').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
        // Stop loading anything to the page
        try {
            window.stop();
        } catch (exception) {
            document.execCommand('Stop');
        }

        // Log out the user
        $.post(
            "Ajax/logout.php",
            function(data) {
        	    location.reload();
            }
        );
    });


    //Shortcut function to load with ajax a local page
    showModal = function(address, title, callback) {
        var dialog = null;
        dialog = bootbox.dialog({
            title: title,
            backdrop: true,
            onEscape: true,
            message: '<div class="text-center"><i class="fa fa-refresh fa-spin fa-fw"</div>'
        });

        dialog.find('.bootbox-body').load(address, function() {
            callback;
        });

    };

});

//Shortuct functions
dialogAlert = function(message, title, callback) {
	bootbox.alert({
		title: title,
	    message: message,
	    callback: callback,
	    backdrop: true
	})
};

dialogWarning = function(message, title, callback) {
	bootbox.alert({
		title: title,
	    message: message,
	    callback: callback,
	    backdrop: true
	})
};

dialogError = function(message, title, callback) {
	bootbox.alert({
		title: title,
	    message: message,
	    callback: callback,
	    backdrop: true
	})
};

dialogConfirm = function(message, title, callback) {
    // Style the message
    StartMessage = '<table><tr><td valign="middle"><i aria-hidden="true" class="padding-right fa fa-question-circle-o fa-5x  fa-red fa-lg  "></i></td><td valign="middle">';
    EndMessage = '</td></tr></table>';

    bootbox.confirm({
        title: title,
        message: StartMessage + message + EndMessage,
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i>&nbsp;&nbsp;No'
            },
            confirm: {
                label: '<i class="fa fa-check"></i>&nbsp;&nbsp;Yes'
            }
        },
        callback: callback,
        backdrop: true
    });
};

dialogPrompt = function(message, value, callback) {
	bootbox.prompt({
	    title: message,
	    value: value,
	    message: "Your message here…",
	    callback: callback,
	    backdrop: true
	});
};

dialogConfirmPassword = function(message, value, title, callback) {
	bootbox.prompt({
	    title: message,
	    inputType: 'password',
	    callback: callback,
	    backdrop: true
	});
};

$('.sidebar-toggle').click( function() {
	 $("#alertBox").css('width', '100%')
});
