<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Onboarding Platform') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- App CSS con Vite -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/css/components/notifications.css', 'resources/js/app.js'])

    <!-- Fallback CSS per ambienti production -->
    @if(env('APP_ENV') === 'production' || !app()->isLocal())
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/components/notifications.css') }}" rel="stylesheet">
    @endif

    <!-- Page Specific CSS -->
    @if (request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.*'))
        <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
    @endif

    @if (request()->routeIs('welcome'))
        <link rel="stylesheet" href="{{ asset('css/pages/welcome.css') }}">
    @endif

    @if (request()->routeIs('admin.*'))
        <link rel="stylesheet" href="{{ asset('css/pages/admin.css') }}">
    @endif

    @if (request()->routeIs('employee.*'))
        <link rel="stylesheet" href="{{ asset('css/pages/employee.css') }}">
    @endif

    <!-- Slot per stili aggiuntivi specifici della pagina -->
    {{ $styles ?? '' }}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="{{ !request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*') ? 'has-fixed-navbar' : '' }} {{ request()->routeIs('welcome') ? 'welcome-page' : '' }}">
    <!-- Navbar Component -->
    <x-navbar />

    @if(request()->routeIs('welcome'))
        {{ $slot }}
    @else
    <div class="container-fluid">
        <div class="row">
            @auth
                <!-- Sidebar -->
                @if(!empty($sidebar))
                    <div class="col-md-3 col-lg-2 sidebar">
                        <div class="position-sticky pt-3">
                            {{ $sidebar }}
                        </div>
                    </div>
                @endif

                <!-- Main content -->
                <main class="{{ !empty($sidebar) ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content' : 'col-12 px-md-4 main-content' }}">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            @else
                <!-- Main content per utenti non autenticati -->
                <main class="col-12 auth-content">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            @endauth
        </div>
    </div>
    @endif

    <x-footer />

    <!-- Slot per script aggiuntivi specifici della pagina -->
    {{ $scripts ?? '' }}
</body>
</html>
