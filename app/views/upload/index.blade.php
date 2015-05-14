@extends('_layouts/blank')

@section('content')
<div class="panel text-center col-xs-4 col-sm-4" >
    <div id="dropzone" class="panel-body row">
    </div>
    <div id="options" class="row">
        <button type="button" class="btn btn-lg btn-warning btn-upload" disabled><i class="glyphicon glyphicon-cloud-upload"></i> upload</button>
        <button type="button" class="btn btn-lg btn-default btn-cancel" style="display: none" ><i class="glyphicon glyphicon-remove"></i> cancel</button>
        {{--testing emails--}}
        <button type="button" class="btn btn-lg btn-default btn-primary" onclick="window.location='{{ url("email/sharetransfer/7") }}'"><i class="glyphicon glyphicon-envelope"></i> Share</button>
    </div>
</div>

@include('upload._partials.uploader')
@stop