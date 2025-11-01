@php
$layoutComponent = (Auth::user()->role == 'admin') ? 'app-layout' : 'user-layout';

$layoutAttributes = (Auth::user()->role == 'admin') ? [] : ['fullwidth' => true];
@endphp

<x-dynamic-component :component="$layoutComponent">

    <div class="container">

        <div class="profile-section">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="profile-section">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>

</x-dynamic-component>