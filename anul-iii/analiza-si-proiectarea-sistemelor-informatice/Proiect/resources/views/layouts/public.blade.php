<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#eaf0e9">
    <meta name="description" content="ELOQUENCE HOSTING — web hosting and infrastructure for digital projects.">
    <title>@yield('title', __('messages.home.title').' — ELOQUENCE HOSTING')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-site">
    <header class="site-header">
        <a class="wordmark" href="{{ route('home') }}" aria-label="ELOQUENCE HOSTING"><span class="brand-symbol" aria-hidden="true"><i></i><i></i><i></i></span><span>ELOQUENCE<span class="wordmark-light"> HOSTING</span></span></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">{{ __('messages.nav.menu') }} <span>+</span></button>
        <nav class="site-nav" id="site-nav" aria-label="{{ __('messages.nav.menu') }}">
            <a href="{{ route('hosting') }}">{{ __('messages.nav.hosting') }}</a><a href="{{ route('vps') }}">{{ __('messages.nav.vps') }}</a><a href="{{ route('domains') }}">{{ __('messages.nav.domains') }}</a><a href="{{ route('email') }}">{{ __('messages.nav.email') }}</a><a href="{{ route('about') }}">{{ __('messages.nav.about') }}</a><a href="{{ route('contact') }}">{{ __('messages.nav.contact') }}</a>
            <a class="nav-contact" href="{{ auth()->check() ? (auth()->user()->role?->name === 'admin' ? route('admin.dashboard') : route('client.dashboard')) : route('login') }}">{{ __('messages.nav.portal') }} <span aria-hidden="true">↗</span></a>
        </nav>
        <div class="site-tools"><a class="language-toggle" href="{{ route('language.switch', app()->getLocale() === 'ro' ? 'en' : 'ro') }}" aria-label="{{ app()->getLocale() === 'ro' ? 'Switch to English' : 'Schimbă în română' }}">{{ app()->getLocale() === 'ro' ? 'EN' : 'RO' }}</a><button class="theme-toggle" type="button" aria-label="{{ __('messages.nav.theme') }}" aria-pressed="false"><span aria-hidden="true">◐</span></button></div>
    </header>
    @if (session('success'))<div class="global-notice" role="status">{{ session('success') }}</div>@endif
    <main id="top">@yield('content')</main>
    <footer class="site-footer"><a class="wordmark" href="{{ route('home') }}"><span class="brand-symbol" aria-hidden="true"><i></i><i></i><i></i></span><span>ELOQUENCE<span class="wordmark-light"> HOSTING</span></span></a><nav class="legal-links" aria-label="{{ __('legal.pages.terms.title') }}"><a href="{{ route('legal.terms') }}">{{ __('legal.pages.terms.title') }}</a><a href="{{ route('legal.privacy') }}">{{ __('legal.pages.privacy.title') }}</a><a href="{{ route('legal.cookies') }}">{{ __('legal.pages.cookies.title') }}</a><a href="{{ route('legal.withdrawal') }}">{{ __('legal.pages.withdrawal.title') }}</a><a href="{{ route('legal.complaints') }}">{{ __('legal.pages.complaints.title') }}</a></nav><p>{{ __('messages.home.footer_tagline') }}<br><span>© {{ date('Y') }} ELOQUENCE HOSTING · {{ app()->getLocale() === 'ro' ? 'PROIECT DEMO' : 'DEMO PROJECT' }}</span></p><a class="back-top" href="#top">{{ __('messages.home.back_top') }} ↑</a></footer>
</body>
</html>
