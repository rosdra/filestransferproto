@extends('_layouts/blank')

@section('content')
<div class="panel text-center col-xs-4 col-sm-4" >
    <div id="dropzone" class="panel row">
    </div>
    <div id="options" class="row">
        <button type="button" class="btn btn-lg btn-warning btn-upload" disabled><i class="glyphicon glyphicon-cloud-upload"></i> upload</button>
        <button type="button" class="btn btn-lg btn-default btn-cancel" style="display: none" ><i class="glyphicon glyphicon-remove"></i> cancel</button>
    </div>
</div>

@include('upload._partials.uploader')
@stop