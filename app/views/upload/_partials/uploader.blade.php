@section('extra-js')
    <script>
        var url_initial = '{{ url("/") }}';
        var url_share = '{{ route("files.share") }}';
    </script>

	{{ HTML::script('jquery/plugins/js/fileupload/vendor/jquery.ui.widget.js') }}	
	{{ HTML::script('jquery/plugins/js/fileupload/jquery.fileupload.js') }}
	{{ HTML::script('jquery/plugins/js/fileupload/jquery.fileupload-process.js') }}
	{{ HTML::script('jquery/plugins/js/fileupload/jquery.fileupload-video.js') }}
	{{ HTML::script('jquery/plugins/js/jstemplate/tmpl.min.js') }}
	{{ HTML::script('jquery/plugins/js/alert.js') }}
	{{ HTML::script('filestransfer/js/app.upload.js') }}
@append

<div class="text-center">
    <form id="fileupload" action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data" accept="*/*">
        <input type="file" id="input-browse" class="block-hide" multiple="multiple" name="files">
    </form>
</div>

<div class="whitebox">
    <div class="arrow-container">
        <div class="arrow select active">1. SELECT</div>
    </div>
    <div class="arrow-container">
        <div class="arrow upload">2. UPLOAD</div>
    </div>
    <div class="arrow-container">
        <div class="arrow sharing">3. SHARE</div>
    </div>
    <div class="clearfix"></div>
    <div id="dropzone" class="row drop">
        <div id="step-1" class="step-active">
            <!-- File List -->
            <div class="list-group items-holder" ></div>
            <div>
                <h2 class="start">Drag & Drop your files</h2>
                or
                <button type="button" class="round grey dz-clickable btn-add">Select files</button>
                <p>
                    Maximum upload single size files: 10 GB<br/>
                    Need more? <a href="#">Upgrade to Professional</a>
                </p>
            </div>
        </div>
        <div id="step-2" style="display:none">
            <div class="col-xs-12">
                <!-- Progress bar -->
                <div class="progress-holder">
                    <div class="row">
                        <h3>UPLOADING</h3>
                        <div class="col-xs-8 col-xs-offset-2">
                            <div class="progress-label">-- of --</div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">--% Complete</span>
                                </div>
                            </div>
                            <div class="progress-rate">--%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="step-3" style="display:none">
            <div class="panel-body col-xs-12">
                <!-- Uploaded File List -->
                <div class="list-group items-holder" ></div>
            </div>
            <div class="col-xs-12">
                <a role="button" class="btn btn-default btn-download" style="display:none">
                 <span class="fa fa-link"> Get the download link</span>
                </a>
                <div class="text-center">
                    <h3>FILES UPLOADED</h3>
                    <i class="fa fa-check-square-o fa-4x"></i>
                </div>
            </div>
        </div>
    </div>
    <div id="options" class="row">
        <button type="button" class="round orange btn-upload" disabled>UPLOAD</button>
        <button type="button" class="round grey btn-cancel" style="display: none" >CANCEL</button>
    </div>
</div>

<script type="text/x-tmpl" id="tmpl-upload-item">
	<a class="list-group-item" href="#file-{%=o.index %}" class="item_{%=o.index %}">
        <h6>
            <span><b>{%= o.filename %}</b> ({%= o.filesize_readable %})</span>
            <span class="has-success pull-right item-state" style="display:none">
                <i class="glyphicon glyphicon-ok"></i>
            </span>
        </h6>
	</a>
</script>