@extends('layouts.portal')
@section('title', 'Tichete de suport')
@section('content')
<div class="portal-welcome"><div><h2>Tichete de suport</h2><p>Deschide o solicitare și urmărește conversația cu echipa.</p></div></div>
<div class="portal-two-column">
    <form class="portal-form-card" action="{{ route('client.tickets.store') }}" method="POST">
        @csrf<h3>Deschide un tichet</h3><p>Descrie pe scurt ce ai întâmpinat.</p>
        <label>Subiect<input name="subject" value="{{ old('subject') }}" required maxlength="180"></label>
        <label>Categorie<select name="category"><option value="general">General</option><option value="billing">Facturare</option><option value="technical">Tehnic</option><option value="domain">Domeniu</option></select></label>
        <label>Serviciu asociat <span class="optional">OPȚIONAL</span><select name="service_id"><option value="">Fără serviciu asociat</option>@foreach ($services as $service)<option value="{{ $service->id }}">{{ $service->service_number }} — {{ $service->product->localizedName() }}</option>@endforeach</select></label>
        <label>Prioritate<select name="priority"><option value="low">Scăzută</option><option value="normal" selected>Normală</option><option value="high">Ridicată</option></select></label>
        <label>Mesaj<textarea name="message" rows="5" required maxlength="4000">{{ old('message') }}</textarea></label>
        <button class="button button-primary">Trimite tichetul <span>↗</span></button>
    </form>
    <div class="ticket-list"><h3>Solicitările tale</h3>
        @forelse ($tickets as $ticket)
            <article><div><span class="table-strong">{{ $ticket->ticket_number }}</span><span class="status-pill status-{{ $ticket->status }}">{{ $ticket->status }}</span></div><h4>{{ $ticket->subject }}</h4><p>{{ ucfirst($ticket->category) }} · prioritate {{ $ticket->priority }} · {{ $ticket->messages->count() }} mesaje · actualizat {{ $ticket->updated_at->format('d.m.Y H:i') }}</p>
                @foreach ($ticket->messages as $message)<blockquote><b>{{ $message->user->name }}</b><br>{{ $message->body }}</blockquote>@endforeach
                @if (in_array($ticket->status, ['open', 'in_progress', 'waiting_client'], true))<form method="POST" action="{{ route('client.tickets.reply', $ticket->id) }}">@csrf<label>Răspunsul tău<textarea name="message" rows="3" required maxlength="4000"></textarea></label><button class="button button-primary">Adaugă răspuns</button></form>@endif
            </article>
        @empty<div class="empty-state">Nu ai deschis tichete încă.</div>@endforelse
    </div>
</div>
@endsection
