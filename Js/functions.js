/**
 *
 * @param status
 * @param menuId
 */
function toggleMenuStatus(status, menuId) {
    if (status === 'false') {
        $('.' + menuId).hide();
        $('#' + menuId).find('i').removeClass('fa-angle-down').addClass('fa-angle-right');
    } else if (status === 'true') {
        $('.' + menuId).show();
        $('#' + menuId).find('i').removeClass('fa-angle-right').addClass('fa-angle-down');
    }
}

/**
 * Routing function. Returns page url based on the provided link
 * @param page
 * @returns {string}
 */
function getPageUrl(page) {
    var url = 'Pages/Suppliers/dashboard.php';
    if (typeof defaultPage !== 'undefined') {
        url = defaultPage;
    }

    // First lets split the page by # character.
    pageArray = page.split('#');
    page = pageArray.pop();
    if (pageArray.length < 1){
        return url;
    }

    var supplierPages = ['supdashboard', 'enginereports', 'domains', 'supplierlogs', 'testzone', 'consumers', 'statistics'];
    var consumerPages = ['suppliers', 'consumerstatistics', 'translatebox', 'availableengines'];
    var accountPages = ['settings', 'apiconfiguration'];
    var controlPanelPages = ['accounts', 'requestlogs', 'logs', 'engines'];
    var mtHubPages = ['about', 'connectors'];

    if ($.inArray(page, supplierPages) > -1) {
        page = page == 'supdashboard' ? 'dashboard' : page;
        url = 'Pages/Suppliers/' + page + '.php';
    } else if ($.inArray(page, consumerPages) > -1) {
        url = 'Pages/Consumers/' + page + '.php';
    } else if ($.inArray(page, accountPages) > -1) {
        url = 'Pages/Account/' + page + '.php';
    } else if ($.inArray(page, controlPanelPages) > -1) {
        url = 'Pages/ControlPanel/' + page + '.php';
    } else if ($.inArray(page, mtHubPages) > -1) {
        url = 'Pages/MtHub/' + page + '.php';
    }

    return url;
}

/**
 *
 * @param message
 */
function logoutUserWithMessage(message) {
    $.post(
        "Ajax/logout.php",
        function(data) {
            // Refresh
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "index.php");
            form.setAttribute("target", "_self");
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "message");
            hiddenField.setAttribute("value",message);
            form.appendChild(hiddenField);
            document.body.appendChild(form);

            form.submit();
        }
    );
}

/**
 * Checks if object has given key. Taken from http://stackoverflow.com/questions/135448/how-do-i-check-if-an-object-has-a-property-in-javascript
 */
function hasOwnProperty(obj, prop){
    var proto = obj.__proto__ || obj.constructor.prototype;

    return (prop in obj) && (!(prop in proto) || proto[ prop ] !== obj[ prop ]);
}

/**
 * Get menu element based on the url
 * @param page
 * @returns {*}
 */
function getMenuElementName(page) {
    var element = page;

    if (page == 'Pages/Suppliers/dashboard.php') {
        element = 'supdashboard';
    } else if (page == 'Pages/Consumers/suppliers.php') {
        element = 'suppliers';
    } else if (page == 'Pages/ControlPanel/accounts.php') {
        element = 'accounts';
    } else if (page == 'Pages/Account/settings.php') {
        element = 'settings';
    }

    return element;
}

/**
 *
 * @param page
 * @returns {boolean}
 */
function goToPage(page) {
    var menuElement = $('#sidebar-menu li a[href="#' + getMenuElementName(page) + '"]');

    // Check if datatable refresh interval is set and if it is clear it
    if (typeof interval !== 'undefined') {
        clearInterval(interval);
    }

    // if the page passed is NOT found to be a menu element (i.e. menuElement is empty)
    if (isEmpty(menuElement)) {
        // get the current active menu element
        menuElement = $('#sidebar-menu li a[class="active"]');
    } else {
        // if the page passed is a menuElement, remove the active status from the old active element
        removeActiveMenuElements();
        // Add active to the new menuElement
        addActiveMenuElement(menuElement);
    }

    var fontClass = menuElement.find('i').attr("class");
    menuElement.find('i').toggleClass(fontClass + ' fa fa-refresh fa-spin fa-fw');
    // Load initial page content
    loadPageContent(page, menuElement, fontClass);

    return false;
}

/**
 *
 * @param page
 * @param menuElement
 * @param fontClass
 */
function loadPageContent(page, menuElement, fontClass) {
    var pageContentElement = $('#pageContent');
    pageContentElement.empty();

    // Check if datatable refresh interval is set and if it is clear it
    if (typeof interval !== 'undefined') {
        clearInterval(interval);
    }

    // Scroll the page to the top
    $('html, body').animate({scrollTop: 0}, 'fast');

    pageContentElement.html(loader).load(page, function() {
        menuElement.find('i').toggleClass(fontClass + ' fa fa-refresh fa-spin fa-fw');
        $('.breadcrumb li a').click(function() {
            var breadcrumbElement = $(this);
            var nextPage = breadcrumbElement.attr('href');

            goToPage(nextPage);

            return false;
        });
    });
}

/**
 * Remove active menu element
 */
function removeActiveMenuElements() {
    $('#sidebar-menu li a').removeClass('active');
}

/**
 * Add active menu element
 * @param menuElement
 */
function addActiveMenuElement(menuElement) {
    menuElement.addClass('active');
}

/**
 * Print flag in datatables
 * @param data
 * @param type
 * @param row
 * @returns {string}
 */
function formatFlag(data, type, row) {
    var icon = '';

    if (data != '') {
        icon = '<div class="inline-block" style="white-space: nowrap;"><span class="flag-icon flag-icon-' + data + '"></span>&nbsp&nbsp;' + data + '</div>';
    }

    return icon;
}

/**
 * Process Curl Info
 * @param info
 */
function processCurlInfo(info) {
    if (info) {
        info = JSON.parse(info);
        $("#httpCode").val(info.http_code ? info.http_code : 'N/A');
        $("#time").val(info.total_time ? info.total_time : 'N/A');
        $("#requestSize").val(info.request_size ? info.request_size : 'N/A');
    }
}

/**
 *
 * @param tableId
 * @returns {Array}
 */
function getSelectedCheckboxValues(tableId) {
    var selectedRows = [];

    $('#' + tableId + ' .dt-checkboxes:checkbox:checked').each(function () {
        selectedRows.push($(this).val());
    });

    return selectedRows;
}

/**
 *
 * @param data
 * @param type
 * @param row
 * @returns {string}
 */
function formatAccountStatus(data, type, row) {
    // Green = active
    // Red = Deleted (Profile and User data deleted)
    var active = row[2];
    var link = '<i class=\"fa fa-user fa-fw fa-lg fa-green\" title="Active" aria-hidden=\"true\"></i>';

    // If the account is not active
    if (active != 1) {
        link = '<i class=\"fa fa-user fa-fw fa-lg fa-red\" title="Inactive" aria-hidden=\"true\"></i>';
    }

    return link;
}

/**
 * Validate email
 * @param email
 * @returns {boolean}
 */
function validateEmail(email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (!emailReg.test(email)) {
        return false;
    }

    return true;
}

/**
 * Clears error messages
 */
function cleanError() {
    $('.error').html('&nbsp;');
}

/**
 * Check if object set
 * @param obj
 * @returns {boolean}
 */
function isEmpty(obj) {
    // null and undefined are "empty"
    if (obj == null) {
        return true;
    }

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0) {
        return false;
    }

    if (obj.length === 0) {
        return true;
    }

    // If it isn't an object at this point
    // it is empty, but it can't be anything *but* empty
    // Is it empty?  Depends on your application.
    if (typeof obj !== "object") {
        return true;
    }

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

/**
 *
 * @param id
 */
function showRequestDetails(id) {
    var link = 'Pages/ControlPanel/Modals/requestlog.php?id=' + id;
    showModal(link, 'Request Details');
}

/**
 *
 * @param data
 * @param type
 * @param row
 * @returns {string}
 */
function formatRequestLog(data, type, row) {
    var icon = '<span id="btnEdit_' + row[0] + '" title="Details" onClick="showRequestDetails(' + row[0] + ')"><i class="fa fa-lg fa-search-plus fa-pointer" aria-hidden="true"></i></span>';

    return icon;
}

/**
 * Format select2 state for consumer list
 * @param state
 * @returns {*}
 */
function formatConsumer(state) {
    if (state.id != 0) {
        return state.text;
    }

    var $state = $(
        '<div class="inline-block" style="white-space: nowrap;"><i class="fa fa-plus fa-fw fa-lg fa-green" aria-hidden="true"></i> ' + state.text + '</div>'
    );

    return $state;
}

/**
 * Format select2 state. We add on the front of the text the country flag
 * @param state
 * @returns {*}
 */
function formatState(state) {
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<div class="inline-block" style="white-space: nowrap;"><span class="flag-icon flag-icon-' + state.id + '"></span>' + state.text + '</div>'
    );

    return $state;
}

/**
 * Show pop up dialogs
 */
function showPopUps() {
    $('[data-toggle="popover"]').popover({html: true});
}

/**
 * Format long strings
 * @param data
 * @param type
 * @param row
 * @returns {string}
 */
function formatLongString(data, type, row) {
    string = data.length > 35 ? data.substring(0, 32) + '...' : data;
    data = data.replace(/"/g, '&quot;');
    var stringDisplay = '<span title=\"' + data + '\">' + string + '</span>';

    return stringDisplay;
}

/**
 * Format email display
 * @param data
 * @param type
 * @param row
 * @returns {string}
 */
function formatEmail(data, type, row) {
    var email = data;

    if (email != null && email.length > 31) {
        email = email.substring(0, 27) + "...";
    }

    var mailImg = '<i class="fa fa-lg fa-envelope-o fa-fw fa-deeporange fa-pointer" aria-hidden="true"></i>';
    var link = "<span title=\"" + row[7] + "\">" + "<a  href=\'mailto:" + row[7] + "\'>" + mailImg + "</a>&nbsp;&nbsp;" + email + "</span>";

    return link;
}

/**
 * Edit engine
 * @param id
 */
function editEngine(id) {
    var link = 'Pages/Suppliers/Modals/engine.php?id=' + id;
    showModal(link, 'Edit Properties');
}

/**
 * Edit supplier
 * @param id
 */
function editSupplier(id) {
    var link = 'Pages/Consumers/Modals/supplier.php?id=' + id;
    showModal(link, 'Edit Properties');
}

/**
 * Format edit properties icon
 * @param data
 * @param type
 * @param row
 * @returns {string}
 */
function formatConsumerEditProperties(data, type, row) {
    // If there is at least on entry in the table lest disable for now adding a new consumer
    $("#btnAddConsumer").prop('disabled', true);
    var icon = '<span id=\"' + row[0] + '\" title=\"Edit properties\" onClick=\"editSupplier(' + row[0] + ')\"><i class=\"fa fa-lg fa-pencil-square-o fa-pointer\" aria-hidden=\"true\"></i></span>';

    return icon;
}

/**
 *
 * @param name
 * @param value
 */
function createCookie(name, value) {
    var days = 30;
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }

    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

/**
 *
 * @param name
 * @returns {*}
 */
function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }

    return null;
}

/**
 * Copy the element text to clipboard.
 * @param elementId
 */
function copyToClipboard(elementId) {
    $("#" + elementId).select();
    document.execCommand('copy');
}
