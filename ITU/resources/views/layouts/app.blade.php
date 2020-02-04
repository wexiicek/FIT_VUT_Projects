<!doctype html>

<!--
* ITU Project 2019/2020
* Flight Search (Team xjurig00, xlinka01, xpukan01)
*
* Author of this file: Dominik Juriga (xjurig00)
*
* -->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    @yield('js')

    <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/esm/popper-utils.js"
            integrity="sha256-Fxwx4JC0VO/4EdYrHbDEBXvboZmi+tHYBlFWev8cZqM=" crossorigin="anonymous"></script>

    <script
        src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
        integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="
        crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
            crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <script src="https://kit.fontawesome.com/dcd5c2c95e.js" crossorigin="anonymous"></script>
</head>
<body>
<div id="app">

    <nav class="navbar navbar-expand-lg">

        <div class="col-xl-3 col-lg-4 col-sm-12 col-12 nav_left">
            <a class="navbar-brand" href="{{ route('home') }}">
                <div class="logo"><img src="{{ asset('logo.png') }}" class="imgLogo"> ITU FLIGHTS</div>
            </a>
            <button class="navbar-toggler menuPosition" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <i class="fas fa-bars" style="color:#fff; font-size:28px;"></i>
                        </span>
            </button>
        </div>


        <div class="collapse navbar-collapse col-xl-9 col-lg-8 col-sm-12 col-12 " id="navbarSupportedContent">

            <ul class="navbar-nav ml-auto">
                @if(Auth::user())
                    <li class="nav-item">
                        <a class="nav-link nav_item firstItem" href="/">History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav_item" href="/">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav_item" href="/">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav_item nav_user"
                           href="{{ route('profile', Auth::user()->username) }}">{{ auth()->user()->name }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav_item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                        document.getElementById('logout-form').submit();">Logout</a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;">
                        @csrf
                    </form>
                @else
                    <li class="nav-item">
                        <button type="button" class="nav_item" data-toggle="modal" data-target="#login_modal"
                                id="modal_login">
                            Log In
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav_item" data-toggle="modal" data-target="#register_modal">
                            Register
                        </button>
                    </li>
                @endif
            </ul>
        </div>


    </nav>

    <main>
        @yield('content')
    </main>

    @include('modals.login')
    @include('modals.register')
    @include('loader.roller')
    @yield('login_modal')
    @yield('register_modal')
    @yield('roller')

</div>
</body>
</html>
