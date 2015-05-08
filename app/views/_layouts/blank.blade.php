<!DOCTYPE html>
<html lang="en">
	<head>
		@include('_includes.title')
		@yield('extra-css')
	</head>
	<body>  
	  	@yield('main scripts')
	  		  	
		<div class="container">
	
			<header class="row">
				@include('_includes.header')
			</header>
		
			<div id="main" class="row">	
                @yield('content')
			</div>
		
			<footer class="row">
				@include('_includes.footer')
			</footer>
		</div>

		@yield('extra-js')
	</body>
</html>
