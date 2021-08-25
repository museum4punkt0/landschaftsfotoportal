<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/landschaftsfotoportal.js') }}" defer ></script>

    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="{{ asset('css/landschaftsfotoportal.css') }}" rel="stylesheet">
    <style>
      .map {
        height: 500px;
        width: 100%;
      }
    </style>
</head>
<body id="page-top">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">
                <img id="sgnLogo" src="{{ asset('storage/images/logos/sgn_logo.svg') }}" width=160 alt="Senckenberg" />
            </a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars ml-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ml-auto">
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('item.gallery') }}#portfolio">Portal</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('search.index') }}">@lang('search.header')</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('item.timeline') }}#timeline">Zeitstrahl</a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('item.map') }}">Karte</a></li>
                    @guest
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('login') }}">@lang('Login') </a></li>
                    @else
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('home') }}">@lang('users.profile')</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            @lang('Logout')
                        </a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
@if (Route::currentRouteName() == 'item.gallery')
    <!-- Masthead -->
    <header class="masthead">
        <div class="container">
            <div class="masthead-subheading">Willkommen!</div>
            <div class="masthead-heading text-uppercase">Landschafts&shy;fotoportal</div>
            <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="#services">Wissen Sie mehr?</a>
        </div>
    </header>
    
    <!-- Services -->
    <section class="page-section" id="services">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-upload fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Upload</h4>
                    <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima maxime quam architecto quo inventore harum ex magni, dicta impedit.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-images fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">@lang('cart.my_own')</h4>
                    <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima maxime quam architecto quo inventore harum ex magni, dicta impedit.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-map fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Karte</h4>
                    <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima maxime quam architecto quo inventore harum ex magni, dicta impedit.</p>
                </div>
            </div>
        </div>
    </section>
@endif
    
    <!-- Include the content section, e.g. gallery, timeline, image details -->
    @yield('content')
    
@if (Route::currentRouteName() == 'item.gallery')
    <!-- Partner Logos -->
    <div class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 col-sm-6 my-3">
                    <a href="https://www.senckenberg.de/" target="_blank"><img class="img-fluid d-block mx-auto" src="{{ asset('storage/images/logos/sgn_logo.png') }}" alt="" /></a>
                </div>
                <div class="col-md-4 col-sm-6 my-3">
                    <a href="https://www.museum4punkt0.de/teilprojekt/forschung-in-museen-erklaeren-verstehen-mitmachen/"><img class="img-fluid d-block mx-auto" src="{{ asset('storage/images/logos/museum4punkt0_logo.png') }}" alt="" /></a>
                </div>
                <div class="col-md-4 col-sm-6 my-3">
                    <a href="https://www.bundesregierung.de/breg-de/suche/kultur-fuer-alle-1543646"><img class="img-fluid d-block mx-auto" src="{{ asset('storage/images/logos/bkm_logo.png') }}" alt="" /></a>
                </div>
            </div>
        </div>
    </div>
@endif
    
    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 text-lg-left">© Senckenberg 2020</div>
                <div class="col-lg-4 my-3 my-lg-0">
                    <a class="btn btn-dark btn-social mx-2" href="#!"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-dark btn-social mx-2" href="#!"><i class="fab fa-facebook-f"></i></a>
                </div>
                <div class="col-lg-4 text-lg-right">
                    <a class="mr-3" href="#!">Impressum</a>
                    <a class="mr-3" href="#!">Kontakt</a>
                    <a href="#!">Danksagung</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
