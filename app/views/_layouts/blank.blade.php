<!DOCTYPE html>
<html lang="en">
	<head>
		@include('_includes.title')
		@yield('extra-css')
	</head>
	<body>  
	  	@yield('main scripts')
	  		  	
		<div class="container-fluid bg-warning">
			<header class="row-fluid">
				@include('_includes.header')
			</header>
		
			<div id="main" class="row-fluid">
                @yield('content')
			</div>
		
			<footer class="row-fluid">
				@include('_includes.footer')
			</footer>
		</div>

		@yield('extra-js')
	</body>
</html>
