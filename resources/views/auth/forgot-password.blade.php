<x-guest-layout>

    <div class="guest-back-link-wrapper">
        <a href="{{ route('login') }}" class="guest-back-link">
            &larr; Back to Login
        </a>
    </div>

    <div class="profile-section" style="padding: 0; box-shadow: none; margin-bottom: 0;">
        <p style="margin-bottom: 1.5rem; text-align: center;">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
        </p>
    </div>

    @if (session('status'))
    <div class="notification notification-success" style="position: static; margin-bottom: 1rem;">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="guest-form-action">
            <button type="submit" class="btn btn-primary btn-full-width">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </form>
</x-guest-layout>