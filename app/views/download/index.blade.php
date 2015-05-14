@extends('_layouts/blank')

@section('content')
<div class="panel text-center col-xs-4 col-sm-4" >
    <div id="downloadzone" class="row panel-body downloadzone">
        @include('download._partials.downloader')
    </div>
    <div id="options" class="row">
        <button type="button" class="btn btn-lg btn-warning btn-download" disabled><i class="glyphicon glyphicon-cloud-download"></i> DOWNLOAD</button>
        <button type="button" class="btn btn-lg btn-default btn-cancel" style="display: none" ><i class="glyphicon glyphicon-remove"></i> CANCEL</button>
        <button type="button" class="btn btn-lg btn-default btn-start" style="display: none" ><i class="glyphicon glyphicon-home"></i> START</button>
        <a id="a-start-downloading" href="#" data-base-url="{{ url("/") }}" style="display: none;">Start Downloading</a>
    </div>
</div>
@stop
