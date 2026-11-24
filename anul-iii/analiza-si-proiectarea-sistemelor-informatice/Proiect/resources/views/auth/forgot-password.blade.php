@extends('layouts.public')
@section('title', 'Recuperare parolă — ELOQUENCE HOSTING')
@section('content')
<section class="auth-page">
    <div class="auth-intro"><h1>Recuperează<br><em>accesul.</em></h1><p>Îți trimitem un link pentru a alege o parolă nouă.</p></div>
    <form class="auth-card" method="POST" action="{{ route('password.email') }}">
        @csrf
        <h2>Ai uitat parola?</h2>
        <p>Introdu adresa folosită la crearea contului.</p>
        @if ($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
        @if (session('status'))<div class="form-success">{{ session('status') }}</div>@endif
        <label>Adresa de e-mail<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus></label>
        <button class="button button-primary auth-submit">Trimite linkul <span>↗</span></button>
        <p class="auth-switch"><a href="{{ route('login') }}">Înapoi la autentificare</a></p>
    </form>
</section>
@endsection
