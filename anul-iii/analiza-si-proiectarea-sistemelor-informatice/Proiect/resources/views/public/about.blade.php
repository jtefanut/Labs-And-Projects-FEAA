@extends('layouts.public')
@section('title', __('messages.pages.about_title').' — ELOQUENCE HOSTING')
@section('content')
<section class="public-page-hero"><h1>{{ __('messages.pages.about_headline_a') }}<br><em>{{ __('messages.pages.about_headline_b') }}</em></h1><p>{{ __('messages.pages.about_intro') }}</p><a class="button button-primary" href="{{ route('contact') }}">{{ __('messages.home.contact_cta') }} <span>↗</span></a></section>
<section class="section about-grid"><div><h2>{{ __('messages.pages.about_values_title_a') }}<br><em>{{ __('messages.pages.about_values_title_b') }}</em></h2></div><div class="about-copy"><p>{{ __('messages.pages.about_body_a') }}</p><p>{{ __('messages.pages.about_body_b') }}</p><a class="section-inline-link" href="{{ route('hosting') }}">{{ __('messages.pages.about_cta') }} <span>↗</span></a></div></section>
@endsection
