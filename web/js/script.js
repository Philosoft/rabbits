function getUniqueArray(array) 
{
    var uniqueArray = [];

    array.forEach(function (arrayValue) {
        arrayValue = arrayValue.trim();

        if (arrayValue !== "" && uniqueArray.indexOf(arrayValue) === -1) {
            uniqueArray.push(arrayValue);
        }
    });

    return uniqueArray;
}

function getUrlsInformation(urls, ajaxUrl, urlsTable, processFunction)
{
    if(typeof urls !== "undefined" && urls.length > 0) {
        var currentUrl = encodeURIComponent(urls[0]);
        jQuery.ajax({
            url: ajaxUrl, 
            dataType: "json",
            method: "get",
            data: {
                url: currentUrl
            },
            success: function (data) {
                if (typeof(processFunction) === 'function') {
                    processFunction(data, urls, ajaxUrl, urlsTable);
                }
            }
        });
    }
}

var processResponse = function processUrlResponse(urlData, urls, ajaxUrl, urlsTable)
{
    if (typeof(urlData) !== 'object' || typeof(urlData.status) == 'undefined') {
        return false;
    }

    var tableBody = urlsTable.find('tbody');
    var tableRow = jQuery(document.createElement('tr')).appendTo(tableBody);
    if (urlData.status !== 'success') {
        jQuery(document.createElement('td')).html(urlData.url).appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
    } else {
        jQuery(document.createElement('td')).html(urlData.url).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.statusCode).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.redirectsNumber).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.finalUri).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.finalStatusCode).appendTo(tableRow); 
    }
    urls.splice(0, 1);
    getUrlsInformation(urls, ajaxUrl, urlsTable, processResponse);
}

var processInfo = function processUrlInfo(urlData, urls, ajaxUrl, urlsTable)
{
    if (typeof(urlData) !== 'object' || typeof(urlData.status) == 'undefined') {
        return false;
    }

    var tableBody = urlsTable.find('tbody');
    var tableRow = jQuery(document.createElement('tr')).appendTo(tableBody);
    if (urlData.status !== 'success') {
        jQuery(document.createElement('td')).html(urlData.url).appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
        jQuery(document.createElement('td')).html('-').appendTo(tableRow); 
    } else {
        jQuery(document.createElement('td')).html(urlData.url).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.title.join('|<br/>')).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.h1.join('|<br/>')).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.description.join('|<br/>')).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.keywords.join('|<br/>')).appendTo(tableRow); 
        jQuery(document.createElement('td')).html(urlData.canonical.join('|<br/>')).appendTo(tableRow); 

    }
    urls.splice(0, 1);
    getUrlsInformation(urls, ajaxUrl, urlsTable, processInfo);
}

jQuery(document).ready(function () {
    jQuery('.js-check-form').click(function (event) {
        event.preventDefault();
        var urlsTextarea = jQuery(this).parents('.js-form-container').find('.js-form-area');
        var urls = urlsTextarea.val().split("\n");

        urls = getUniqueArray(urls);
        urlsTextarea.val(urls.join("\n"));
    });

    jQuery('.js-clear-form').click(function (event) {
        event.preventDefault();
        var urlsTable = jQuery(this).parents('.js-form-container').find('.js-form-table');
        urlsTable.find('tbody').empty();
    });

    jQuery('.js-response-get').click(function (event) {
        event.preventDefault();
        var urlsTable = jQuery(this).parents('.js-form-container').find('.js-form-table');
        var urlsTextarea = jQuery(this).parents('.js-form-container').find('.js-form-area');
        var urls = urlsTextarea.val().split("\n");
        getUrlsInformation(urls, '/url-data/url-response', urlsTable, processResponse);
    });

    jQuery('.js-info-get').click(function (event) {
        event.preventDefault();
        var urlsTable = jQuery(this).parents('.js-form-container').find('.js-form-table');
        var urlsTextarea = jQuery(this).parents('.js-form-container').find('.js-form-area');
        var urls = urlsTextarea.val().split("\n");
        getUrlsInformation(urls, '/url-data/url-info', urlsTable, processInfo);
    });
});
