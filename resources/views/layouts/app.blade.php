<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <!-- TODO: move to /ressources/sass/backend.css CSS file-->
    <style>
      .map {
        height: 500px;
        width: 100%;
      }
      .thumbnail-table {
        max-width: 150px;
        max-height: 100px;
      }
      .fa-btn {
        font-size: 0.6rem;
      }

    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                    @auth
                        @can('show-admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('item.index') }}">{{ __('items.header') }}</a>
                            </li>
                            @if(config('ui.taxa'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('taxon.index') }}">{{ __('taxon.header') }}</a>
                            </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a id="importNavbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" >
                                    {{ __('import.header') }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="imprtNavbarDropdown">
                                    <a class="dropdown-item" href="{{ route('import.csv.upload') }}">
                                        {{ __('lists.header') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('import.items.upload') }}">
                                        {{ __('items.header') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('import.taxa.upload') }}">
                                        {{ __('taxon.header') }}
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="configNavbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" >
                                    {{ __('common.config') }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="configNavbarDropdown">
                                    <a class="dropdown-item" href="{{ route('list.item_types') }}">
                                        {{ __('lists.item_types') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('list.column_groups') }}">
                                        {{ __('lists.column_groups') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('list.translations') }}">
                                        {{ __('lists.translations') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('list.index') }}">
                                        {{ __('lists.header') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('column.index') }}">
                                        {{ __('columns.header') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('colmap.index') }}">
                                        {{ __('colmaps.header') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('module.index') }}">
                                        {{ __('modules.header') }}
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="adminNavbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" >
                                    {{ __('common.admin_tools') }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminNavbarDropdown">
                                @can('show-super-admin')
                                    <a class="dropdown-item" href="{{ route('attribute.index') }}">
                                        {{ __('attributes.header') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('list.internal') }}">
                                        {{ __('lists.internal_header') }}
                                    </a>
                                @endcan
                                    <a class="dropdown-item" href="{{ route('titles.create') }}">
                                        {{ __('items.add_titles') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('detail.orphans') }}">
                                        {{ __('items.remove_orphans') }}
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.index') }}">{{ __('users.header') }}</a>
                            </li>
                            @if(config('ui.comments'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('comment.all') }}">{{ __('comments.header') }}</a>
                            </li>
                            @endif
                        @endcan
                    @endauth
                            <li class="nav-item dropdown">
                                <a id="langNavbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" >
                                    {{ __('common.language') }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="langNavbarDropdown">
                                @foreach(Config::get('languages') as $lang => $language)
                                    <a class="dropdown-item" href="{{ route('locale', $lang) }}">
                                        {{ $language }}
                                    </a>
                                @endforeach
                                </div>
                            </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        {{ __('users.profile') }}
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
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
