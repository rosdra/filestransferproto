@section('extra-css')
	{{ HTML::style('filestransfer/css/filestransfer.upload.css') }}
@append
@section('extra-js')
	{{ HTML::script('jquery/plugins/js/fileupload/vendor/jquery.ui.widget.js') }}	
	{{ HTML::script('jquery/plugins/js/fileupload/jquery.fileupload.js') }}
	{{ HTML::script('jquery/plugins/js/fileupload/jquery.fileupload-process.js') }}
	{{ HTML::script('jquery/plugins/js/fileupload/jquery.fileupload-video.js') }}
	{{ HTML::script('jquery/plugins/js/jstemplate/tmpl.min.js') }}
	{{ HTML::script('filestransfer/js/filestransfer.upload.js') }}
@append

<div class="container text-center start-zone">
    <form id="fileupload" action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data" accept="*/*">
        <input type="file" id="input-browse" class="block-hide" multiple="multiple" name="files">
    </form>
</div>

<script type="text/x-tmpl" id="tmpl-ext-dropzone">
	<div class="addmore">
        <div class="box">
            <div class="col-xs-12" style="color:light-gray">
                <div class="list-group items-holder" ></div>
            </div>
            <div class="col-xs-12">
                <a role="button">
                    <div class="plus-icon"> </div>
                    Drag & Drop your files<br/>
                    or<br/>
                    <button type="button" class="btn btn-prev-thumb">Select files</button><br/>
                    <small>
                        Maximum upload single size files: 10 GB
                        Need more? <a href="#">Upgrade to Professional</a>;
                    </small>
                 </a>
            </div>
        </div>
	</div>
</script>
<script type="text/x-tmpl" id="tmpl-upload-item">
	<a class="list-group-item" href="#file-{%=o.index %}" class="item_{%=o.index %}">
        <h6>
            {%= o.filename %}
            <span class="badge">{%= o.filesize %} {%= o.filesizeunit %}</span>
        </h6>
	</a>
</script>