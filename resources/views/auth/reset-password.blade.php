@extends('layouts.public')
@section('title', 'Parolă nouă — ELOQUENCE HOSTING')
@section('content')
<section class="auth-page">
    <div class="auth-intro"><h1>Setează o<br><em>parolă nouă.</em></h1><p>Alege o parolă de cel puțin 10 caractere.</p></div>
    <form class="auth-card" method="POST" action="{{ route('password.update') }}">
        @csrf
        <h2>Parolă nouă</h2>
        @if ($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
        <input type="hidden" name="token" value="{{ $token }}">
        <label>Adresa de e-mail<input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"></label>
        <label>Parola nouă<input type="password" name="password" required minlength="10" autocomplete="new-password"></label>
        <label>Confirmă parola<input type="password" name="password_confirmation" required minlength="10" autocomplete="new-password"></label>
        <button class="button button-primary auth-submit">Salvează parola <span>↗</span></button>
    </form>
</section>
@endsection
