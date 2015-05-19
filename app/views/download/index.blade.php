@extends('_layouts/blank')

@section('extra-css')
    {{ HTML::style('filestransfer/css/download.css') }}
@stop

@section('content')
<div class="text-center col-sm-4 col-xs-12" >
    <div id="downloadzone" class="whitebox">
        @include('download._partials.downloader')

        <div id="options" class="row">
            <button type="button" class="btn btn-lg btn-warning btn-download round" disabled>DOWNLOAD</button>
            <button type="button" class="btn btn-lg btn-default btn-cancel" style="display: none" ><i class="glyphicon glyphicon-remove"></i> CANCEL</button>
            <button type="button" class="btn btn-lg btn-default btn-start" style="display: none" ><i class="glyphicon glyphicon-home"></i> START</button>
        </div>
    </div>
</div>
@stop
