@extends('layouts.public')
@section('title', $document['title'].' — ELOQUENCE HOSTING')
@section('content')
<section class="legal-page">
    <div class="legal-header"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a><h1>{{ $document['title'] }}</h1><p>{{ $document['intro'] }}</p></div>
    <div class="legal-draft" role="note">{{ __('legal.draft_notice') }}<br>{{ __('legal.company_fields') }}</div>
    <div class="legal-sections">
        @foreach($document['sections'] as $section)
            <section><h2>{{ $section['title'] }}</h2><p>{{ $section['body'] }}</p></section>
        @endforeach
    </div>
    @if($page === 'complaints')
        <nav class="legal-authority-links" aria-label="{{ $document['title'] }}">
            <a href="https://anpc.ro/" target="_blank" rel="noopener noreferrer">{{ __('legal.website_link') }} ↗</a>
            <a href="https://anpc.ro/sal/" target="_blank" rel="noopener noreferrer">{{ __('legal.anpc_link') }} ↗</a>
            <a href="https://reclamatiisal.anpc.ro/" target="_blank" rel="noopener noreferrer">{{ __('legal.sal_link') }} ↗</a>
        </nav>
    @endif
</section>
@endsection
