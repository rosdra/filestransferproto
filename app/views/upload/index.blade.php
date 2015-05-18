@extends('_layouts/blank')

@section('content')

<div id="uploadcontainer" class="col-sm-12 col-md-4" >
    @include('upload._partials.uploader')
</div>

<div id="shareoptionstitle" class="col-sm-12 col-md-8 text-center share" style="display: none;">
    <h3>{{ HTML::image('filestransfer/img/arrow_share_sx.png') }} HOW DO YOU WANT TO SHARE YOUR FILES? {{--{{ HTML::image('filestransfer/img/arrow_share_dx.png') }}--}}</h3>
</div>

<div id="sharecontainer" class="panel col-md-4 col-sm-4" style="display: none;">
    <div id="sharezone" class="share">
        @include('upload._partials.share_by_email')
    </div>
    <div id="options" class="row">
        <button type="button" class="round orange btn-share">SHARE</button>
        <button type="button" class="round grey btn-share-again" style="display: none"><i class="fa fa-repeat"></i> Share again</button>
    </div>
</div>

@stop