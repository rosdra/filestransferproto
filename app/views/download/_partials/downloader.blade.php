@section('extra-css')
	{{ HTML::style('filestransfer/css/app.css') }}
@append
@section('extra-js')
	{{ HTML::script('jquery/plugins/js/fileupload/vendor/jquery.ui.widget.js') }}
	{{ HTML::script('jquery/plugins/js/jstemplate/tmpl.min.js') }}
	{{ HTML::script('jquery/plugins/js/alert.js') }}
	{{ HTML::script('filestransfer/js/app.download.js') }}
@append

<div class="box-solid">
    <div id="step-1" class="step-active">
        <form id="filedownload" action="{{ url('/download/'.$transfer->id.'/'.$pid) }}" method="GET" enctype="multipart/form-data" accept="*/*">
            <div class="col-xs-12">
                <div class="text-center">
                    <h3>DOWNLOAD FILES...</h3>
                    <i class="fa fa-cloud-download fa-4x"></i>
                </div>
            </div>
            <div class="col-xs-12">
                <ul class="list-group items-holder">
                @foreach($transfer->files as $transfer_file)
                    <li class="list-group-item">
                       {{$transfer_file->original_name}}
                    </li>
                @endforeach
                </ul>
            </div>
        </form>
    </div>
    <div id="step-2" style="display:none">
        <div class="col-xs-12">
            <!-- Progress bar -->
            <div class="progress-holder">
                <div class="row">
                    <h3>DOWNLOADING</h3>
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
        <div class="col-xs-12">
            <div class="text-center">
                <h3>FILES DOWNLOADED</h3>
                <i class="fa fa-check-square-o fa-4x"></i>
            </div>
        </div>
        <div class="col-xs-12">
            <h5>
                Like it? Start yourself using Download.it<br/>
                If you need more, <a href="#">upgrade to Professional</a>
            </h5>
        </div>
    </div>
</div>

<script>
    var url_download_progress = '{{url("/progress/".$pid)}}';
</script>
