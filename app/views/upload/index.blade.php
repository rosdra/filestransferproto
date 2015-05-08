@extends('_layouts/blank')

@section('content')
<div class="panel text-center col-xs-3 col-sm-3" >
    <div id="dropzone" class="row">
    </div>
    <div id="options" class="row">
        <button type="button" class="btn btn-primary btn-upload"><i class="glyphicon glyphicon-open"></i> upload</button>
    </div>
</div>

@include('upload._partials.uploader')
@stop