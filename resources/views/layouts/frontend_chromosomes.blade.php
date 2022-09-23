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

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="{{ asset('css/chromosomes.css') }}" rel="stylesheet">
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
      .map {
        height: 550px;
        width: 100%;
      }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-light sticky-top flex-nowrap p-0">
        <a class="navbar-brand col-3 mr-0 p-0" href="{{ url('/') }}">
            <img class="site-logo mx-auto d-none d-md-block"
                src="{{ asset('storage/images/chromosomes/logos/chromosomes_logo.png') }}" />
            <img class="site-logo mx-auto d-block d-md-none"
                src="{{ asset('storage/images/chromosomes/logos/chromosomes_logo_klein.png') }}" />
        </a>
        <button class="navbar-toggler position-absolute d-lg-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="col-7 col-lg-8 px-0">
            <span class="site-heading">@lang('chromosomes.site_heading')</span><br>
            <span class="site-subheading">@lang('chromosomes.site_subheading')</span>
        </div>
        <div class="col-2 col-lg-1">
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Menu -->
            <nav id="sidebarMenu" class="col-lg-3 d-lg-block d-print-none sidebar collapse">
                <div class="p-lg-3">
                    <div class="sidebar-sticky pt-3 px-3">
                        <ul class="nav flex-column">

                            @section('sidebar_menu_items')
                            @show

                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Content Area -->
            <main role="main" class="col-lg-9 ml-sm-auto pl-lg-0 pr-lg-3">
                <!-- Toolbar -->
                <div class="d-flex d-print-none justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3">
                    <!-- Search input -->
                    <form class="form-inline" action="{{ route('search.index') }}#searchResults" method="GET">
                        <input
                            class="form-control mr-sm-2"
                            type="text"
                            name="full_text"
                            placeholder="@lang('search.full_text')"
                            aria-label="@lang('search.full_text')"
                        />
                        <button class="btn btn-sm btn-outline-primary d-none d-md-block my-2 my-sm-0" type="submit">@lang('search.search')</button>
                    </form>

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

                <!-- Main content area -->
                <div class="row">
                    <div class="col">
                        <div class="main-content p-3">
                        @yield('content')
                        </div>
                    </div>
                    <!-- Optional (image) module area -->
                    @yield('content-module-right')
                </div>

                <!-- Footer Area with Logos and Links -->
                <div class="d-print-none pt-3">
                    <div class="footer p-3">
                        <div class="row">
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2 align-self-center">
                                <!-- Authentication Links -->
                                <ul class="navbar-nav px-3">
                                @guest
                                    <li class="nav-item text-nowrap">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('users.internal_login') }}</a>
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
                                            <a class="dropdown-item" href="{{ route('home') }}">
                                                {{ __('users.dashboard') }}
                                            </a>
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
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2 align-self-center">
                                <a href="https://www.senckenberg.de/" target="_blank">
                                    <img class="img-fluid" src="{{ asset('storage/images/chromosomes/logos/sgn_logo.png') }}" />
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2 align-self-center">
                                <a href="https://www.dfg.de/" target="_blank">
                                    <img class="img-fluid" src="{{ asset('storage/images/chromosomes/logos/dfg_logo.png') }}" />
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2 align-self-center">
                                <a href="https://www.bmuv.de/" target="_blank">
                                @if(app()->getLocale() == 'de')
                                    <img class="img-fluid" src="{{ asset('storage/images/chromosomes/logos/BMUV_2021_de.gif') }}" />
                                @else
                                    <img class="img-fluid" src="{{ asset('storage/images/chromosomes/logos/BMUV_2021_en.gif') }}" />
                                @endif
                                </a>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2 align-self-center">
                                Aktualisiert in einem Förderprojekt des
                                <a href="https://www.bfn.de/" target="_blank">
                                @if(app()->getLocale() == 'de')
                                    <img class="img-fluid" src="{{ asset('storage/images/chromosomes/logos/BfN_2022_de.gif') }}" />
                                @else
                                    <img class="img-fluid" src="{{ asset('storage/images/chromosomes/logos/BfN_2022_en.gif') }}" />
                                @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        
        </div>
    </div>
    
</body>
</html>
