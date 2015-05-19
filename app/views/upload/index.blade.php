@extends('_layouts/blank')

@section('content')

<div id="uploadcontainer" class="col-sm-12 col-md-4" >
    @include('upload._partials.uploader')
</div>

<div id="shareoptionstitle" class="col-sm-12 col-md-8 text-center share" style="display: none;">
    <h3>{{ HTML::image('filestransfer/img/arrow_share_sx.png') }} HOW DO YOU WANT TO SHARE YOUR FILES? {{--{{ HTML::image('filestransfer/img/arrow_share_dx.png') }}--}}</h3>
</div>

<div id="sharecontainer" class="col-sm-12 col-md-4" style="display: none;">
    @include('upload._partials.share_by_email')
</div>

@stop