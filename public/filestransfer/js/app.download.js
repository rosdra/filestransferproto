$(function () {
    "user strict";

    var downloader = DownloadHandler();
    downloader.init();
});

function DownloadHandler(){
    var init = function () {
        var currentStep = 1;
        // setup initial step
        setupDownloadZone(currentStep);

        $('.btn-download').on('click', function (e) {
            // prevent closing page
            setConfirmClosePage();

            currentStep = 2;
            setupDownloadZone(currentStep);

            var form = $('#filedownload');
            var url = form.attr('action');
            var method = form.attr('method');
            var data = form.serializeArray();
            // download
            $.get(url, data)
                .done(function (response) {
                    //$url = response.zip;
                    //setTimeout(function () { window.location = $url; }, 500);
                })
                .fail(function (response) {

                });
            window.setTimeout(function () {
                // start progress
                getProgress();
            },200);
        });
    };

    var setConfirmClosePage = function () {
        window.onbeforeunload = function (e) {
            var msg = 'Are you sure to leave this page ?';
            e = e || window.event;
            if (e) e.returnValue = msg;
            return msg;
        }
    };

    var setupDownloadZone = function (step) {
        var oldStepHolder = $('#downloadzone').find(".step-active");
        oldStepHolder.hide();
        oldStepHolder.removeClass("step-active");

        var newStepHolder = $('#downloadzone').find('#step-' + step);
        newStepHolder.show();
        newStepHolder.addClass("step-active");

        if (step == 1) {
            if ($('.items-holder').length > 0) {
                $('.btn-download').removeAttr('disabled');
            }
        } else if (step == 2) {
            $('.btn-download').hide();
            $('.btn-cancel').show();
        } else if (step == 3) {
            $('.btn-cancel').hide();
            $('.btn-start').show();
        }
    };

    var initFormHelper = function () {
    };

    var setProgressAll = function (data) {
        if (data.finished === false) {
            showProgress(data.progress, data.downloaded, data.total);
            window.setTimeout(function () {
                getProgress();
            },200);
        }
        else {
            redirectProgress('Finished.');

            currentStep = 3;
            window.setTimeout(function () {
                setupDownloadZone(currentStep);
            }, 1000);

            hideProgress();

            $url = data.zip;
            setTimeout(function () {
                window.onbeforeunload = null;
                window.location = $url;
            }, 500);
        }
    };

    var getProgress = function () {
        $.get(url_download_progress, function (data) {
            setProgressAll(data);
        });
    };

    var showProgress = function (rate, loaded, total) {
        var $p = $('.progress-holder');
        if (!$p.is(':visible')) $p.show();
        $p.find('.progress-label').text(getReadableFileSizeString(loaded) + ' of ' + getReadableFileSizeString(total) + ' completed');
        $p.find('.progress-bar').css('width', rate + '%');
        $p.find('.progress-rate').text(rate + '%');
    };

    var hideProgress = function () {
        $('.progress-holder').hide();
    };

    var redirectProgress = function (text) {
        var $p = $('.progress-holder');
        $p.find('.progress-rate').text(text);
    };

    var clearClientStorage = function () {
        if (window.localStorage) {
            localStorage.clear();
        }
    };

    var getReadableFileSizeString = function (fileSizeInBytes) {
        var i = -1;
        var byteUnits = [' KB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
        do {
            fileSizeInBytes = fileSizeInBytes / 1024;
            i++;
        } while (fileSizeInBytes > 1024);

        return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
    };

    return {init: init};
}


