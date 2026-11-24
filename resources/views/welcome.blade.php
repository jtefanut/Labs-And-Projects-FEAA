@extends('layouts.public')

@section('content')
<section class="hero">
    <div class="hero-grid" aria-hidden="true"></div>
    <div class="hero-copy">
        <h1>{{ __('messages.home.headline_a') }}<br><em>{{ __('messages.home.headline_b') }}</em><br>{{ __('messages.home.headline_c') }}</h1>
        <p class="hero-intro">{{ __('messages.home.intro') }}</p>
        <div class="hero-actions"><a class="button button-primary" href="#servicii">{{ __('messages.home.services_cta') }} <span aria-hidden="true">↘</span></a><a class="text-link" href="{{ route('login') }}">{{ __('messages.home.portal_cta') }} <span aria-hidden="true">↗</span></a></div>
    </div>
    <div class="hero-art" aria-label="ELOQUENCE HOSTING infrastructure illustration">
        <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
        <div class="server-stack"><div class="server-unit unit-back"><span class="server-lights"><i></i><i></i><i></i><i></i></span><span class="unit-slot"></span></div><div class="server-unit unit-mid"><span class="server-lights"><i></i><i></i><i></i><i></i></span><span class="unit-slot"></span></div><div class="server-unit unit-front"><span class="server-lights"><i></i><i></i><i></i><i></i></span><span class="unit-slot"></span></div><div class="server-base"></div></div>
    </div>
</section>
<section class="section services-section" id="servicii">
    <div class="section-heading"><div><h2>{{ __('messages.home.services_title_a') }}<br><em>{{ __('messages.home.services_title_b') }}</em></h2></div><div class="heading-aside"><p>{{ __('messages.home.services_intro') }}</p><a class="section-inline-link" href="{{ route('contact') }}">{{ __('messages.home.contact_cta') }} <span>↗</span></a></div></div>
    <div class="service-grid">
        @foreach ($services as $service)
        <a class="service-card" href="{{ $service['route'] }}"><span class="service-glyph">{{ $service['glyph'] }}<span>↗</span></span><h3>{{ $service['title'] }}</h3><p>{{ $service['summary'] }}</p><span class="service-arrow">{{ __('messages.home.view_service') }} <b>↗</b></span></a>
        @endforeach
    </div>
</section>
<section class="section pricing-section" id="hosting">
    <div class="section-heading"><div><h2>{{ __('messages.home.pricing_title_a') }}<br><em>{{ __('messages.home.pricing_title_b') }}</em></h2></div><div class="heading-aside"><p>{{ __('messages.home.pricing_note') }}</p><a class="section-inline-link" href="{{ route('hosting') }}">{{ __('messages.home.order') }} <span>↗</span></a></div></div>
    <div class="plans-grid">
        @foreach ($plans as $index => $plan)
        <article class="plan-card {{ $plan->is_featured ? 'plan-featured' : '' }}"><div class="plan-top">@if ($plan->is_featured)<span class="plan-badge">{{ __('messages.home.recommended') }}</span>@endif</div><h3>{{ $plan->localizedName() }}</h3><p class="plan-description">{{ $plan->localizedSummary() }}</p><div class="plan-price"><strong>@money($plan->price)</strong><span class="price-period">/ {{ $plan->billing_period === 'yearly' ? __('messages.home.annual') : __('messages.home.monthly') }}</span></div><a class="plan-link" href="{{ route('register') }}">{{ __('messages.home.order') }} <span aria-hidden="true">↗</span></a><ul class="feature-list">@foreach ($plan->localizedFeatures() as $feature)<li><span aria-hidden="true">+</span>{{ $feature }}</li>@endforeach</ul></article>
        @endforeach
    </div>
</section>
<section class="infrastructure" id="infrastructura"><div class="infra-left"><h2>{{ __('messages.home.portal_title_a') }}<br><em>{{ __('messages.home.portal_title_b') }}</em></h2><p class="infra-intro">{{ __('messages.home.portal_text') }}</p><a class="button button-outline" href="{{ route('register') }}">{{ __('messages.home.account') }} <span aria-hidden="true">↗</span></a></div><div class="infra-panel">
        <div class="portal-benefits">
            <article><h3>{{ __('messages.portal.services') }}</h3><p>{{ __('messages.home.benefit_services') }}</p></article>
            <article><h3>{{ __('messages.portal.invoices') }}</h3><p>{{ __('messages.home.benefit_invoices') }}</p></article>
            <article><h3>{{ __('messages.portal.tickets') }}</h3><p>{{ __('messages.home.benefit_tickets') }}</p></article>
        </div>
    </div></section>
<section class="contact-cta"><div><h2>{{ __('messages.home.contact_title_a') }}<br><em>{{ __('messages.home.contact_title_b') }}</em></h2></div><a class="button button-dark" href="{{ route('contact') }}">{{ __('messages.home.contact_cta') }} <span aria-hidden="true">↗</span></a></section>
@endsection
