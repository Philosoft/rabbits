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

function getUrlsInformation(urls, ajaxUrl, urlsTable, urlsProgressBar, processFunction)
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
                    processFunction(data, urls, ajaxUrl, urlsTable, urlsProgressBar);
                }
            },
            error: function () {
                if (typeof(processFunction) === 'function') {
                    processFunction(null, urls, ajaxUrl, urlsTable, urlsProgressBar);
                }
            }
        });
    }
}

function processInputData(clickedButton, urlRoute, ajaxFunction) 
{
    var urlsTable = clickedButton.parents('.js-form-container').find('.js-form-table');
    var urlsTextarea = clickedButton.parents('.js-form-container').find('.js-form-area');
    var urlsProgressBar = clickedButton.parents('.js-form-container').find('.js-progress-bar');
    var urls = urlsTextarea.val().split("\n");

    urls = getUniqueArray(urls);
    urlsTextarea.val(urls.join("\n"));
    if (urls.length < 1) {
        return false;
    }

    setProgressBar(urlsProgressBar, urls);
    getUrlsInformation(urls, urlRoute, urlsTable, urlsProgressBar, ajaxFunction);
}

function setProgressBar (progressBar, data)
{
    progressBar.removeClass('progress-bar-success');
    progressBar.attr('aria-valuemin', 0);
    progressBar.attr('aria-valuenow', 0);
    progressBar.attr('aria-valuemax', data.length);
    progressBar.css('width', '0%');
    progressBar.find('.js-progress-bar-label').html('');
}

function incrementProgressBar(progressBar)
{
    var finalValue = progressBar.attr('aria-valuemax');
    var currentValue = progressBar.attr('aria-valuenow');

    currentValue++;
    var persents = Math.round((currentValue * 100) / finalValue);

    progressBar.attr('aria-valuenow', currentValue);
    progressBar.css('width', persents + '%');
    progressBar.find('.js-progress-bar-label').html(persents + '%');

    if (persents > 99) {
        progressBar.addClass('progress-bar-success');
    }
}

var processResponse = function processUrlResponse(urlData, urls, ajaxUrl, urlsTable, urlsProgressBar)
{
    var tableBody = urlsTable.find('tbody');
    var tableRow = jQuery(document.createElement('tr')).appendTo(tableBody);

    if (typeof(urlData) !== 'object' || urlData.status !== 'success') {
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

    incrementProgressBar(urlsProgressBar);
    urls.splice(0, 1);
    getUrlsInformation(urls, ajaxUrl, urlsTable, urlsProgressBar, processResponse);
}

var processInfo = function processUrlInfo(urlData, urls, ajaxUrl, urlsTable, urlsProgressBar)
{
    var tableBody = urlsTable.find('tbody');
    var tableRow = jQuery(document.createElement('tr')).appendTo(tableBody);
    if (typeof(urlData) !== 'object' || urlData.status !== 'success') {
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

    incrementProgressBar(urlsProgressBar);
    urls.splice(0, 1);
    getUrlsInformation(urls, ajaxUrl, urlsTable, urlsProgressBar, processInfo);
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
        processInputData(jQuery(this), '/url-data/url-response', processResponse);
    });

    jQuery('.js-info-get').click(function (event) {
        event.preventDefault();
        processInputData(jQuery(this), '/url-data/url-info', processInfo);

    });
});
