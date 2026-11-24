@extends('layouts.portal')
@section('title', 'Editează clientul')
@section('content')
<div class="portal-welcome"><div><h2>{{ $client->user->name }}</h2><p>Actualizează datele de contact folosite în portal și în documentele demonstrative.</p></div></div>
<form class="portal-form-card" method="POST" action="{{ route('admin.clients.update', $client) }}">
    @csrf @method('PUT')
    <div class="form-grid">
        <label>Nume<input name="name" value="{{ old('name', $client->user->name) }}" maxlength="120" required></label>
        <label>E-mail<input type="email" name="email" value="{{ old('email', $client->user->email) }}" maxlength="180" required></label>
        <label>Companie<input name="company" value="{{ old('company', $client->company) }}" maxlength="160"></label>
        <label>Telefon<input name="phone" value="{{ old('phone', $client->phone) }}" maxlength="40"></label>
        <label>Adresă<input name="address" value="{{ old('address', $client->address) }}" maxlength="180"></label>
        <label>Oraș<input name="city" value="{{ old('city', $client->city) }}" maxlength="100"></label>
        <label>Cod țară<input name="country" value="{{ old('country', $client->country) }}" minlength="2" maxlength="2" required></label>
    </div>
    <button class="button button-primary">Salvează datele <span>↗</span></button>
</form>
@endsection
