<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>File Explorer</title>
        <!-- Fonts -->
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> --}}

        {{-- Bootstrao 5 CSS --}}
        <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> --}}
        <!-- Styles -->
        {{-- JQuery 3 --}}
        <script src="{{ asset('jquery/3.6.3/jquery.min.js')}}"></script>
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script> --}}
    </head>
    <body>
        <nav class="navbar">
            <div class="container-lg">
                <a href="/" class="navbar-brand fw-bold text-primary">File Explorer</a>
                <ul class="nav justify-content-end">
                    @if(session()->has('username') && session()->has('password'))
                    <li class="nav-item">
                      <a href='/logout' class="nav-link "><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                    </li>
                    @endif
                  </ul>
            </div>
        </nav>
        @yield('content')

        {{-- fontawesome --}}
        {{-- <script src="https://kit.fontawesome.com/2a90b2a25f.js" crossorigin="anonymous"></script> --}}
        {{-- Bootstrap 5 JS --}}
        <script src="{{ asset('bootstrap/js/bootstrap.bundle.js') }}"></script>
        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> --}}
        
    </body>
</html>