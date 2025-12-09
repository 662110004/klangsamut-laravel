<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="guest-container">

        <div class="guest-card">

            <img src="{{ asset('pictures/Web-Logo.png') }}" alt="KlangSamut Logo" class="guest-logo">

            @if (isset($title))
            <h1 class="guest-title">
                {{ $title }}
            </h1>
            @endif

            {{ $slot }}
        </div>
    </div>

</body>

</html>