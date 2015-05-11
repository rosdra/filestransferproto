<!-- headers -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- title -->
<title>@yield('title','Files Transfer')</title>

<!-- Style & Scripts -->
<!-- jQuery -->
{{ HTML::script('//code.jquery.com/jquery.js') }}
{{-- <script src="assets/jquery/jquery-1.10.2.min.js"></script> --}}

<!-- bootstrap -->
{{--{{ HTML::style('bootstrap/css/bootstrap.min.css') }}--}}
{{--{{ HTML::script('bootstrap/js/bootstrap.min.js') }}--}}
{{ HTML::style('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css') }}
{{ HTML::script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js') }}
<link href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet">
<!-- prettify -->
{{ HTML::script('prettify/run_prettify.js') }}

<!-- bootstrap-dialog -->
{{ HTML::style('bootstrap-dialog/css/bootstrap-dialog.min.css') }}
{{ HTML::script('bootstrap-dialog/js/bootstrap-dialog.min.js') }}
