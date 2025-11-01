<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KlangSamut') }}</title>

    <link rel="icon" href="{{ asset('pictures/Web-Logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <div class="user-layout-container">

        <header class="user-header">

            <div class="user-header-left">
                <a href="{{ route('user.home') }}" class="sidebar-logo" style="height: 100%; border: none; padding: 0;">
                    <img src="{{ asset('pictures/Web-Logo.png') }}" alt="KlangSamut Logo" class="sidebar-logo-img">
                    <span class="sidebar-logo-text">KlangSamut</span>
                </a>
                <div class="user-header-nav">
                    <a href="{{ route('books.index') }}" class="user-nav-link">Browse All Books</a>
                </div>
            </div>

            <div class="user-header-right">
                @auth
                <div class="user-menu" x-data="{ open: false }">
                    <button @click="open = !open" class="user-menu-button">
                        <div>{{ Auth::user()->name }}</div>
                        <svg class="hamburger-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" class="user-dropdown" x-cloak>
                        <a href="{{ route('profile.edit') }}" class="dropdown-link">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="dropdown-link"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <main class="user-main-content">
            @yield('content')
        </main>

        @include('layouts.partials.footer-user')
    </div>
</body>

</html>