@section('extra-js')
	{{ HTML::script('jquery/plugins/js/fileupload/vendor/jquery.ui.widget.js') }}
	{{ HTML::script('jquery/plugins/js/jstemplate/tmpl.min.js') }}
	{{ HTML::script('jquery/plugins/js/alert.js') }}
	{{ HTML::script('filestransfer/js/app.download.js') }}
@append

<div class="box-solid download">
    <div id="step-1" class="step-active">
        <form id="filedownload" action="{{ url('/download/'.$transfer->id.'/'.$pid) }}" method="GET" enctype="multipart/form-data" accept="*/*">
            <div class="col-xs-12">
                <div class="text-center">
                    <h2>DOWNLOAD FILES...</h2>
                    {{ HTML::image('filestransfer/img/icon_download.png') }}
                </div>
            </div>
            <div class="col-xs-12">
                <ul class="list-group download-file-list">
                @foreach($transfer->files as $transfer_file)
                    <li class="list-group-item">
                       <h6><b>
                               @if(strlen($transfer_file->original_name) > 30)
                                   {{substr($transfer_file->original_name,0,27)."..."}}
                               @else
                                   {{$transfer_file->original_name}}
                               @endif
                           </b> ({{$transfer_file->size_readable}})</h6>
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
                    <h2>DOWNLOADING</h2>
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
                <h2 class="downloaded">FILES DOWNLOADED</h2>
                {{ HTML::image('filestransfer/img/icon_done.png') }}
            </div>
        </div>
        <div class="col-xs-12 download-final-message">
            <h5>
                Like it? Start yourself using Download.it<br/>
                If you need more, <a href="#">upgrade to Professional</a>
            </h5>
        </div>
    </div>
</div>

<script>
    var url_initial = '{{ url("/") }}';
    var url_download_progress = '{{url("/progress/".$pid)}}';
</script>
