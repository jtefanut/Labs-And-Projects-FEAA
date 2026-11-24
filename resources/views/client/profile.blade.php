@extends('layouts.portal')
@section('title', 'Profil')
@section('content')
<div class="portal-welcome"><div><h2>Datele profilului</h2><p>Actualizează datele de contact și informațiile companiei.</p></div></div>
<form class="profile-form" action="{{ route('client.profile.update') }}" method="POST">@csrf @method('PUT')<div class="form-grid"><label>Nume complet<input name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="120"></label><label>Adresă de e-mail<input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="180"></label><label>Companie<input name="company" value="{{ old('company', $client->company) }}" maxlength="160"></label><label>Telefon<input name="phone" value="{{ old('phone', $client->phone) }}" maxlength="40"></label><label>Adresă<input name="address" value="{{ old('address', $client->address) }}" maxlength="180"></label><label>Oraș<input name="city" value="{{ old('city', $client->city) }}" maxlength="100"></label></div><button class="button button-primary">Salvează modificările <span>↗</span></button></form>
@endsection
