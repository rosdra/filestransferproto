$(function () {
    "user strict";
    $('.browse_files').on('click', function () {
        $('#input-browse').focus().click();
        return false;
    });

    var $dropzone = $('#dropzone');
    $dropzone.addClass("dropzone");
    // render HTML for thumbnail item
    var dropZoneBoxHtml = tmpl("tmpl-ext-dropzone", {});
    $dropzone.append(dropZoneBoxHtml);

    $link = $dropzone.find('a');
    $link.on('click', function () {
        $('#input-browse').focus().click();
        return false;
    });

    $('.btn-upload').show();

    window.onbeforeunload = false;
    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });

    var uploader = FIVE();
    uploader.init();
})

function FIVE() {
    var destinationAfterUpload = '';
    var filesToUpload = [];

    var init = function () {
        var jqXHR, uploadIndex = 0;

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

                //Read photo at client
                $.each(data.files, function (index, file) {
                    uploadIndex++;// Index for each photo
                    var is1stFile = false;
                    if ($lstItems.length == 0) {
                        is1stFile = true;
                        uploadIndex = 1;
                    }

                    // Extract EXIF data
                    var tdata = $.extend({}, {title: file.name, count: uploadIndex, active: is1stFile});

                    // render HTML for new item
                    var itemHtml = tmpl("tmpl-upload-item", {
                        index: uploadIndex,
                        filename: file.name,
                        filesize: file.size / 1000000,
                        filesizeunit: 'MB'
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
                // var progress = parseInt(data.loaded / data.total * 100, 10);
            },

            progressall: function (e, data) {
                var progress = 0;
                if (data.total) {
                    progress = parseInt(data.loaded / data.total * 100, 10);
                }
                showProgress(progress);
            },

            submit: function (e, data) {
                if ($('#file-' + data.context).length != 0) { //dont send removed items
                    var additionData = $('#file-' + data.context + ' form').serializeArray();
                    data.formData = additionData;
                    return true;
                } else {
                    return false;
                }
            },
            success: function (e, data) {
                if (e == 'failed') {
                     alertify.alert(av_limit.text.upload_failed)
                }
            },

            done: function (e, data) {
                if (e == 'failed') {
                     alertify.alert(av_limit.text.upload_failed)
                }
            },

            fail: function () {
                 alertify.alert(av_limit.text.upload_failed);
            },

            stop: function (e) {
                redirectProgress('Finished.');
                window.onbeforeunload = false;
                window.setTimeout(function () {
                    redirectProgress(av_limit.text.upload_completed);
                }, 1000);
				window.setTimeout(function(){
					window.location.href = av_limit.redirect_url;
				}, 2000);
            }
        });
        
        // + Add more
        $(document).on('click', '.btn-add', function (e) {
            $('#input-browse').focus().click();
            return false;
        });

        /*-- Binding events --*/
        //Cancel (remove) a photo
        $(document).on('click', '.btn-remove', function (e) {
            var r = confirm('Are you sure to remove this photo');
            if (r == true) {
                // remove photo thumbnail + editor
                $('.edit-zone.active').remove();
                if ($('.thumbnails-holder .active').prev().length) {
                    $('.thumbnails-holder .active').prev().find('a').trigger('click').end().end().remove();
                } else {
                    $('.thumbnails-holder .active').next().find('a').trigger('click').end().end().remove();
                }
                updateUploadEditor();

                // If no photo remains, reset start-zone
                if ($('.upload-thumbnail').length == 0) {
                    $('.start-zone').show();
                    $('.uploader').addClass('block-hide');
                    is1stFile = true;
                    filesToUpload = [];
                }
            }
        });

        $('.btn-upload').on('click', function (e) {
            $('.btn-upload').off('click').css({opacity: 0.5, cursor: 'wait'});
            $('html, body').animate({scrollTop: 0}, 1000);

            $('.upload-thumbnail').parent().removeClass('active');
            // $('.edit-zone').removeClass('active');
            $('.map').addClass('disabled');
            $('.edit-zone input, .edit-zone textarea, .edit-zone select').attr('readonly', 'true');

            for (i in filesToUpload) {
                filesToUpload[i].submit();
            }
        });

        $(document).on('click', '#myTab a', function (e) {
            e.preventDefault();
            if (!$(this).closest('li').hasClass('active')) {
                $(this).tab('show');
                tabSelect($(this));
            }
        })
    }

    var tabSelect = function (_this) {
        $('.preview-zone img').attr('src', _this.find('img').attr('src'));
        initFormHelper();
    };

    var confirmClosePage = function () {
        window.onbeforeunload = function (e) {
            var msg = 'Are you sure to leave this page ?';
            e = e || window.event;
            if (e) e.returnValue = msg;
            return msg;
        }
    };

    var updateUploadEditor = function () {

    };

    var initFormHelper = function () {
    };

    var clearClientStorage = function () {
        if (window.localStorage) {
            localStorage.clear();
        }
    };

    return {init: init};
}