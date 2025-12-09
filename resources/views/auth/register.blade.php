<x-guest-layout>
    <div class="guest-back-link-wrapper">
        <a href="{{ route('home') }}" class="guest-back-link">
            &larr; Back to Welcome
        </a>
    </div>

    <x-slot name="title">
        {{ __('Register to KLANGSAMUT') }}
    </x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
            @error('password')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="guest-links-container justify-end">
            <a class="guest-link" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>

        <div class="guest-form-action">
            <button type="submit" class="btn btn-primary">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-guest-layout>