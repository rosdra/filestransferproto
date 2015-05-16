@extends('_layouts/blank')

@section('content')

<div id="uploadcontainer" class="panel text-center col-md-4 col-sm-4" >
    <div id="dropzone" class="panel-body row">
        @include('upload._partials.uploader')
    </div>
    <div id="options" class="row">
        <button type="button" class="btn btn-lg btn-warning btn-upload" disabled><i class="glyphicon glyphicon-cloud-upload"></i> upload</button>
        <button type="button" class="btn btn-lg btn-default btn-cancel" style="display: none" ><i class="glyphicon glyphicon-remove"></i> cancel</button>
    </div>
</div>
<div id="sharecontainer" class="panel text-center col-md-4 col-sm-4" style="display: none">
    <div id="sharezone" class="panel-body">
        @include('upload._partials.share_by_email')
    </div>
    <div id="options" class="row">
        <button type="button" class="btn btn-lg btn-primary btn-share"><i class="glyphicon glyphicon-envelope"></i> SHARE</button>
        <button type="button" class="btn btn-lg btn-default btn-share-again" style="display: none"><i class="fa fa-repeat"></i> Share again</button>
    </div>
</div>

@stop