@section("header")
 
    <!-- Navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                {{ HTML::link('/', 'Files Transfer',null, array('class' => 'navbar-brand')) }}
            </div>
            <!-- Everything you want hidden at 940px or less, place within here -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    @if ( Auth::guest() )
                    @else
                    @endif
                </ul>
            </div>
        </div>
    </div>
@show


	