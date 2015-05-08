<!DOCTYPE html>
<html lang="en">
	<head>
		@include('_includes.title')	    
	</head>
	<body>				
  			  	
		<div class="container">
	
			<header class="row">
				@include('_includes.header')
			</header>
		
			<div class="row panel-body">	
				@if (Session::get('message'))
					<div class="alter alter-success">
						{{Session::get('message')}}
					</div>			
				@endif
						
			    {{-- left content --}}
			    <div class="col-md-6 panel-body">       
			        @yield('navigator_content')        
			    </div>
			    
			    {{-- right content --}}
			    <div class="col-md-6 panel-body"> 
			        @yield('form_content')
			    </div>			     
			</div>			
			
			<footer class="row">
				@include('_includes.footer')
			</footer>
		
		</div>  	
	  	
	</body>
</html>
