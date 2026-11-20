<x-guest-layout>
    <h1>{{ __('Log in') }}</h1>

    <x-auth-session-status :status="session('status')"/>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')"/>
        </div>

        <div class="field">
            <x-input-label for="password" :value="__('Password')"/>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password"/>
            <x-input-error :messages="$errors->get('password')"/>
        </div>

        <label for="remember_me" class="checkbox">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>

        <div class="form-actions">
            @if (Route::has('password.request'))
                <a class="link small" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif

            <x-primary-button>{{ __('Log in') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
