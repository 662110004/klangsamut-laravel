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
    <div class="layout-container">

        <nav class="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('pictures/Web-Logo.png') }}" alt="KlangSamut Logo" class="sidebar-logo-img">
                <span class="sidebar-logo-text">KLANGSAMUT</span>
            </a>

            <div class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                @auth
                @if(Auth::user()->role == 'admin')
                <span class="sidebar-header">Admin Menu</span>
                <a href="{{ route('admin.books.admin_index') }}" class="sidebar-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                    Manage Books
                </a>
                <a href="{{ route('admin.authors.index') }}" class="sidebar-link {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}">
                    Manage Authors
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    Manage Categories
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    Manage Users
                </a>
                @endif
                @endauth
            </div>
        </nav>

        <div class="main-content">

            <header class="top-header">
                <div></div>

                @auth
                <div class="user-menu" x-data="{ open: false }">
                    <button @click="open = !open" class="user-menu-button">
                        <div>{{ Auth::user()->name }}</div> <svg class="hamburger-icon" fill="currentColor" viewBox="0 0 20 20">
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
                @else
                <div class="user-menu header-guest-menu">
                    <a href="{{ route('login') }}" style="color: #374151; font-weight: 500; text-decoration: none;">Log in</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" style="color: #374151; font-weight: 500; text-decoration: none;">Register</a>
                    @endif
                </div>
                @endauth

            </header>

            <main class="page-content">

                @if (session('success') || session('error'))
                <div
                    x-data="{ show: false }"
                    x-init="
            /* 1. รอให้หน้าโหลดเสร็จ (nextTick) */
            $nextTick(() => { 
                show = true; /* 2. สั่งให้ 'show' (เริ่มอนิเมชัน) */
                
                /* 3. ตั้งเวลา 3 วินาที เพื่อซ่อน */
                setTimeout(() => show = false, 3000); 
            })
        "
                    :class="{ 'show': show }"
                    class="notification {{ session('success') ? 'notification-success' : 'notification-error' }}"
                    x-cloak>
                    <span>
                        {{ session('success') ?? session('error') }}
                    </span>

                    <span @click="show = false" class="notification-close">&times;</span>
                </div>
                @endif

                {{ $slot }}

            </main>
            @include('layouts.partials.footer-admin')

        </div>
    </div>
</body>

</html>