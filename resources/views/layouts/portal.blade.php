<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173b32"><title>@yield('title') · ELOQUENCE HOSTING</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="portal-body">
@php
    $isAdmin = auth()->user()->role?->name === 'admin';
    $isPaymentOperator = auth()->user()->role?->name === 'payment_operator';
    $menu = $isPaymentOperator ? [
        [__('messages.operator.title'), 'operator.payments', null],
    ] : ($isAdmin ? [
        [__('messages.portal.dashboard'), 'admin.dashboard', null],
        [__('messages.portal.clients'), 'admin.section', 'clienti'],
        [__('messages.portal.users'), 'admin.section', 'utilizatori'],
        [__('messages.portal.products'), 'admin.section', 'produse'],
        [__('messages.portal.orders'), 'admin.section', 'comenzi'],
        [__('messages.portal.services'), 'admin.section', 'servicii'],
        [__('messages.portal.invoices'), 'admin.section', 'facturi'],
        [__('messages.portal.tickets'), 'admin.section', 'tichete'],
        [__('messages.portal.servers'), 'admin.section', 'servere'],
        [__('messages.portal.suppliers'), 'admin.section', 'furnizori'],
        [__('messages.portal.reports'), 'admin.section', 'rapoarte'],
        [__('messages.portal.audit'), 'admin.section', 'audit'],
    ] : [
        [__('messages.portal.dashboard'), 'client.dashboard', null],
        [__('messages.portal.my_services'), 'client.services', null],
        [__('messages.portal.my_orders'), 'client.orders', null],
        [__('messages.portal.invoices'), 'client.invoices', null],
        [__('messages.portal.support_tickets'), 'client.tickets', null],
        [__('messages.portal.profile'), 'client.profile', null],
    ]);
@endphp
<div class="portal-shell">
    <aside class="portal-sidebar">
        <a class="portal-brand" href="{{ $isAdmin ? route('admin.dashboard') : ($isPaymentOperator ? route('operator.payments') : route('client.dashboard')) }}">
            <span class="portal-mark">EH</span><span>ELOQUENCE<small>{{ $isAdmin ? __('messages.portal.admin') : ($isPaymentOperator ? __('messages.operator.title') : __('messages.portal.client_area')) }}</small></span>
        </a>
        <nav class="portal-nav" aria-label="{{ $isAdmin ? __('messages.portal.admin') : ($isPaymentOperator ? __('messages.operator.title') : __('messages.portal.client_area')) }}">
            @foreach($menu as [$label, $routeName, $parameter])
                @php $url = $parameter ? route($routeName, $parameter) : route($routeName); $active = $parameter ? request()->route('section') === $parameter : request()->routeIs($routeName); @endphp
                <a href="{{ $url }}" class="{{ $active ? 'active' : '' }}"><span class="nav-square" aria-hidden="true"></span>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="sidebar-bottom">
            @if(auth()->user()->role?->name === 'admin' && auth()->user()->client)<a href="{{ route('client.dashboard') }}">{{ __('messages.portal.client_area') }} ↗</a>@endif
            <a href="{{ route('home') }}">{{ __('messages.portal.public_site') }} ↗</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">{{ __('messages.portal.sign_out') }} <span>↗</span></button></form>
        </div>
    </aside>
    <div class="portal-main">
        <header class="portal-topbar">
            <div><h1>@yield('title')</h1></div>
            <div class="portal-tools"><a class="language-toggle" href="{{ route('language.switch', app()->getLocale() === 'ro' ? 'en' : 'ro') }}">{{ app()->getLocale() === 'ro' ? 'EN' : 'RO' }}</a><button class="theme-toggle" type="button" aria-label="{{ __('messages.nav.theme') }}" aria-pressed="false"><span aria-hidden="true">◐</span></button></div>
            <div class="account-chip"><span class="account-avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span><span>{{ auth()->user()->name }}<small>{{ $isAdmin ? __('messages.portal.admin') : __('messages.portal.client_area') }}</small></span></div>
        </header>
        @if(session('success'))<div class="portal-notice">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="portal-error">{{ $errors->first() }}</div>@endif
        <main class="portal-content">@yield('content')</main>
        <footer class="portal-footer"><a href="{{ route('legal.terms') }}">{{ __('legal.pages.terms.title') }}</a><a href="{{ route('legal.privacy') }}">{{ __('legal.pages.privacy.title') }}</a><span>© {{ date('Y') }} ELOQUENCE HOSTING</span></footer>
    </div>
</div>
</body>
</html>
