<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Profile') }}</h1>
    </x-slot>

    <div class="stack" style="max-width: 640px">
        <div class="card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
