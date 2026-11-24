@extends('layouts.public')
@section('title', 'Autentificare — ELOQUENCE HOSTING')
@section('content')
<section class="auth-page">
    <div class="auth-intro"><h1>Totul despre<br><em>contul tău.</em></h1><p>Administrează serviciile, comenzile, facturile și tichetele de suport dintr-un singur loc.</p></div>
    <form class="auth-card" method="POST" action="{{ route('login.store') }}">
        @csrf
        <h2>Bine ai revenit.</h2><p>Autentifică-te pentru a continua.</p>
        @if($errors->any())
            <div class="form-error">{{ $errors->first() }}</div>
        @endif
        @if(session('status'))
            <div class="form-success">{{ session('status') }}</div>
        @endif
        <label>Adresa de e-mail<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
        <label>Parola<input type="password" name="password" autocomplete="current-password" required></label>
        <label class="remember-label"><input type="checkbox" name="remember" value="1"> Păstrează-mă autentificat</label>
        <p class="auth-switch"><a href="{{ route('password.request') }}">Ai uitat parola?</a></p>
        <button class="button button-primary auth-submit">Autentificare <span>↗</span></button>
        <p class="auth-switch">Nu ai cont? <a href="{{ route('register') }}">Creează unul</a></p>
        <div class="demo-access"><b>Conturi demonstrative</b><span>Moana / Stefanut / Stanciu Laurentiu · EloquenceDemo123!</span><span>Eloquence User · eloquence.user&#64;demo.eloquence.local · ClientDemo123!</span></div>
    </form>
</section>
@endsection
