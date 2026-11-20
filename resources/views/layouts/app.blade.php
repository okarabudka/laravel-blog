@php use App\Models\TextWidget; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle ?: config('app.name') }}</title>
    <meta name="description" content="{{ $metaDescription ?: '' }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @livewireStyles
</head>
<body>

<header class="site-header">
    <div class="container">
        <a class="site-title" href="{{ route('home') }}">{{ TextWidget::getTitle('header') ?: config('app.name') }}</a>
        <div class="site-tagline">{!! TextWidget::getContent('header') !!}</div>
    </div>
</header>

<nav class="topics" x-data="{ open: false }">
    <div class="container topics-bar">
        <button type="button" class="topics-toggle" @click="open = ! open">
            Topics <x-heroicon-o-chevron-down class="icon"/>
        </button>

        <div class="topics-list" :class="{ 'is-open': open }">
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Home</a>
            @foreach($categories as $category)
                <a href="{{ route('by-category', $category) }}"
                   @class(['is-active' => request('category')?->slug === $category->slug])>{{ $category->title }}</a>
            @endforeach
            <a href="{{ route('about') }}" @class(['is-active' => request()->routeIs('about')])>About us</a>
        </div>

        <div class="account">
            @auth
                <div class="dropdown" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="dropdown-trigger" @click="open = ! open">
                        {{ Auth::user()->name }} <x-heroicon-o-chevron-down class="icon"/>
                    </button>
                    <div class="dropdown-panel" x-show="open" x-cloak>
                        <a class="dropdown-link" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-link">{{ __('Log Out') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container page">
    @isset($header)
        <div class="page-header">{{ $header }}</div>
    @endisset

    {{ $slot }}
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            @foreach($categories as $category)
                <a href="{{ route('by-category', $category) }}">{{ $category->title }}</a>
            @endforeach
            <a href="{{ route('about') }}">About us</a>
        </div>
        <div>&copy; {{ date('Y') }} {{ config('app.name') }}</div>
    </div>
</footer>

@livewireScripts
</body>
</html>
