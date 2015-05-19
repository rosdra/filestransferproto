$(function () {
    "user strict";

    /*var $dropzone = $('#dropzone');
    $dropzone.addClass("dropzone");
    // render HTML for thumbnail item
    var dropZoneBoxHtml = tmpl("tmpl-ext-dropzone", {});
    $dropzone.append(dropZoneBoxHtml);
*/
    window.onbeforeunload = false;
    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });

    var uploader = Upload_Handler();
    uploader.init();
})

function Upload_Handler() {
    var destinationAfterUpload = '';
    var filesToUpload = [];

    var init = function () {
        var jqXHR, uploadIndex = 0, currentStep = 1, transferId = 0;

        $('#fileupload').fileupload({
            progressInterval: 50,
            bitrateInterval: 250,
            maxFileSize: 10000000, //10MB
            //acceptFileTypes: /(\.|\/)(jpe?g)$/i, // default is all
            // singleFileUploads: false,//if TRUE (default), "add" will be call multiple times
            sequentialUploads: true,
            maxNumberOfFiles: 10,
            disableImageResize: true,
            //imageMaxWidth: 1600,
            //imageMaxHeight: 1600,
            uploadTemplateId: null,
            downloadTemplateId: null,
            add: function (e, data) {
                var $lstItems = $('.items-holder');

                //Read file at client
                $.each(data.files, function (index, file) {
                    uploadIndex++;// Index for each photo
                    var is1stFile = false;
                    if ($lstItems.length == 0) {
                        is1stFile = true;
                        uploadIndex = 1;
                        currentStep = 1;
                    }
                    setupDropZone(currentStep);

                    // Extract EXIF data
                    var tdata = $.extend({}, {title: file.name, count: uploadIndex, active: is1stFile});

                    // render HTML for new item
                    var itemHtml = tmpl("tmpl-upload-item", {
                        index: uploadIndex,
                        filename: file.name,
                        filesize_readable: getFileSizeReadable(file.size)
                    });
                    $lstItems.append(itemHtml);

                    data.context = uploadIndex;//KEY POINT, GRRRRRRRR !!!
                    // List of files to upload
                    filesToUpload.push(data);
                });
            },

            send: function (e, data) {
                jqXHR = data.xhr();
            },

            progress: function (e, data) {
                //var progress = parseInt(data.loaded / data.total * 100, 10);
            },

            progressall: function (e, data) {
                var progress = 0;
                if (data.total) {
                    progress = parseInt(data.loaded / data.total * 100, 10);
                }
                showProgress(progress, data.loaded, data.total);
                console.log('Progress:' + progress);
            },

            submit: function (e, data) {
               /* if($('#file-' + data.context).length != 0) { //dont send removed items
                    var additionData = $('#file-' + data.context + ' form').serializeArray();
                    data.formData = additionData;
                    return true;
                } else {
                    return false;
                }*/
                return true;
            },
            success: function (e, data) {
                if (e == 'failed') {
                     alertify.alert('upload failed')
                }else{
                    console.log(data);
                    console.log(e);
                    console.log('upload was successful');

                    transferId = e.transfer_id;
                    console.log('transferId: ' + transferId);
                }
            },

            done: function (e, data) {
                if (e == 'failed') {
                    alertify.alert('upload failed')
                }
                console.log(data);
            },

            fail: function () {
                alertify.alert('upload Failed')
                console.log(data);
            },

            stop: function (e) {
                redirectProgress('Finished.');
                window.onbeforeunload = false;
                /*window.setTimeout(function () {
                    redirectProgress(av_limit.text.upload_completed);
                }, 1000);
				window.setTimeout(function(){
					window.location.href = av_limit.redirect_url;
				}, 2000);*/

                currentStep = 3;
                window.setTimeout(function(){
                    setupDropZone(currentStep);
                }, 1000);

                hideProgress();
            }
        });

        // check if dropzone exist
        if($('#dropzone').length > 0){
            $('#fileupload').fileupload(
                'option',
                'dropZone',
                $('#dropzone')
            );
        }
        
        // + Add more
        $(document).on('click', '.btn-add', function (e) {
            $('#input-browse').focus().click();
            return false;
        });

        /*-- Binding events --*/
        //Cancel (remove) a photo
        $(document).on('click', '.btn-remove', function (e) {
            var r = confirm('Are you sure to remove this file');
            if (r == true) {
                // remove file item
                $('.edit-zone.active').remove();
                if ($('.thumbnails-holder .active').prev().length) {
                    $('.thumbnails-holder .active').prev().find('a').trigger('click').end().end().remove();
                } else {
                    $('.thumbnails-holder .active').next().find('a').trigger('click').end().end().remove();
                }

                // If no photo remains, reset start-zone
                if ($('.upload-thumbnail').length == 0) {
                    $('.uploader').addClass('block-hide');
                    is1stFile = true;
                    filesToUpload = [];
                }
            }
        });

        $('.btn-upload').on('click', function (e) {
            currentStep = 2;

            $('.btn-upload').off('click').css({opacity: 0.5, cursor: 'wait'});
            $('html, body').animate({scrollTop: 0}, 1000);

            for (i in filesToUpload) {
                filesToUpload[i].submit();
            }

            setupDropZone(currentStep);
        });

        $('.btn-cancel').on('click', function (e) {
            window.setTimeout(function(){
                window.location.href = url_initial;
            }, 0);
        });

        $('.btn-share').on('click', function (e) {
            var form = $('#fileshare');
            var url = form.attr('action');
            var method = form.attr('method');
            var data = form.serializeArray();

            data.push({'name': 'transfer_id', 'value': transferId})
            // share
            $.get(url, data)
                .always(function (response) {
                    $('#share-step-1').hide();
                    $('.btn-share').hide();

                    $('#share-step-2').show();
                    $('.btn-share-again').show();
                })
                .fail(function (response) {

                });
        });

        $('.btn-share-again').on('click', function (e) {
            $('#share-step-1').show();
            $('.btn-share').show();

            $('#share-step-2').hide();
            $('.btn-share-again').hide();
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

    var setupDropZone = function (step) {
        var oldStepHolder = $('#dropzone').find(".step-active");
        oldStepHolder.hide();
        oldStepHolder.removeClass("step-active");

        var newStepHolder = $('#dropzone').find('#step-'+step);
        newStepHolder.show();
        newStepHolder.addClass("step-active");

        if(step == 1) {
            if ($('.items-holder').length == 0) {
                newStepHolder.find('div.plus-icon').show();
                newStepHolder.find('h2').html('Drag & Drop your files');
                newStepHolder.find('.btn-add').html('Select files');
                newStepHolder.find('.items-holder').removeClass("ih-added");
                // disable upload
                $('.btn-upload').attr('disabled', 'disabled');
            }else {
                newStepHolder.find('h2').html('Drag & Drop more files');
                newStepHolder.find('div.plus-icon').hide();
                newStepHolder.find('.btn-add').html('Add more files');
                newStepHolder.find('.items-holder').addClass("ih-added");
                // enable upload
                $('.btn-upload').removeAttr('disabled');
                // prevent closing page
                setConfirmClosePage();
            }
            $('#sharespacer').hide();
            // active header step
            $(".arrow").removeClass("active");
            $(".arrow.select").addClass("active");

        }else if(step == 2){
            $('.btn-upload').hide();
            $('.btn-cancel').show();
            $('#sharespacer').hide();
            // active header step
            $(".arrow").removeClass("active");
            $(".arrow.upload").addClass("active");
        }else if(step == 3){
            var items = newStepHolder.find('.items-holder');
            items.addClass("ih-completed");
            items.find('.item-success').show();
            //unbind closing page
            window.onbeforeunload = null;
            // show share
            $('#sharecontainer').show();
            $('#shareoptionstitle').show();
            $('#sharespacer').show();
            // active header step
            $(".arrow").removeClass("active");
            $(".arrow.sharing").addClass("active");
        }
    };

    var initFormHelper = function () {
    };

    var showProgress = function (rate, loaded, total) {
        var $p = $('.progress-holder');
        if (!$p.is(':visible')) $p.show();
        $p.find('.progress-label').text(getFileSizeReadable(loaded) + ' of ' + getFileSizeReadable(total) + ' completed');
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

    var getFileSizeReadable = function(fileSizeInBytes) {
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
