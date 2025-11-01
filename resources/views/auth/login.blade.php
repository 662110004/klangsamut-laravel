<x-guest-layout>
    <x-slot name="title">
        {{ __('Login To KLANGSAMUT') }}
    </x-slot>

    @if (session('status'))
    <div class="notification notification-success" style="position: static; margin-bottom: 1rem;">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
            @error('password')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="guest-links-container">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 shadow-sm" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
            <a class="guest-link" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
            @endif
        </div>

        <div class="guest-form-action">
            <button type="submit" class="btn btn-primary">
                {{ __('Log in') }}
            </button>
        </div>

        <div style="text-align: center; margin-top: 1rem;">
            <a class="guest-link" href="{{ route('register') }}">
                {{ __("Don't have an account? Register") }}
            </a>
        </div>
    </form>
</x-guest-layout>