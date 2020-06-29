<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-dark sticky-top flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 p-0" href="{{ url('/') }}">
            <img src="{{ asset('storage/images/logos/taxa_banner_klein.jpg') }}" />
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <ul class="navbar-nav px-3">
            <!-- Authentication Links -->
            @guest
                <li class="nav-item text-nowrap">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item text-nowrap">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown text-nowrap">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Menu -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
              <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link active" href="#">
                      Home <span class="sr-only">(current)</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Projekte & Partner
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Gattungen
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      Integrations
                    </a>
                  </li>
                </ul>

              </div>
              <!-- Logo Area -->
              <div class="sidebar-footer pt-3">
                  <img src="{{ asset('storage/images/logos/sgn_logo.png') }}" width=170 vspace=10 />
                  <img src="{{ asset('storage/images/logos/hausknecht_logo.png') }}" width=170 vspace=10 />
              </div>
            </nav>
            
            <!-- Content Area -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- Language Dropdown -->
                        <div class="nav-item dropdown">
                            <a id="navbarDropdown" class="btn btn-sm btn-outline-secondary dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('common.language') }} <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @foreach(Config::get('languages') as $lang => $language)
                                <a class="dropdown-item" href="{{ route('locale', $lang) }}">
                                    {{ $language }}
                                </a>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                @yield('content')
                
            </main>
        
        </div>
    </div>
    
</body>
</html>
