<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KlangSamut') }}</title>

    <link rel="icon" href="{{ asset('pictures/Web-Logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="antialiased">
    <div class="landing-container">

        <header class="landing-header">
            <div class="landing-logo-wrapper">
                <img src="{{ asset('pictures/Web-Logo.png') }}" alt="KlangSamut Logo" class="landing-logo">
                <span>KlangSamut</span>
            </div>
            <div class="landing-auth-links">
                <a href="{{ route('login') }}" class="btn btn-secondary">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            </div>
        </header>

        <main class="landing-content">
            <div class="landing-hero">
                <h1 class="landing-title">Welcome to KlangSamut</h1>
                <p class="landing-subtitle">
                    Your personal encyclopedia for books and authors.
                </p>
                <p class="landing-description">
                    Discover new books, learn about your favorite authors, and categorize your reading journey. KlangSamut is a digital catalog designed to help you explore and manage a world of literature.
                </p>
                <div class="landing-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Get Started</a>
                </div>
            </div>
        </main>

        <footer class="landing-footer-wrapper">
            &copy; {{ date('Y') }} KlangSamut. All rights reserved.
        </footer>

    </div>
</body>

</html>