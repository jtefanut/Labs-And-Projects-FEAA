@extends('layouts.portal')
@section('title', 'Tichet ' . $ticket->ticket_number)
@section('content')
<div class="portal-welcome"><div><h2>{{ $ticket->subject }}</h2><p>{{ $ticket->ticket_number }} · {{ $ticket->client->user->name }} · {{ $ticket->category }} · prioritate {{ $ticket->priority }}</p></div><span class="status-pill status-{{ $ticket->status }}">{{ $ticket->status }}</span></div>
<div class="ticket-list">
    @foreach ($ticket->messages as $message)<article><b>{{ $message->user->name }} @if ($message->user->role?->name === 'admin')<span class="optional">ECHIPA</span>@endif</b><p>{{ $message->created_at->format('d.m.Y H:i') }}</p><blockquote>{{ $message->body }}</blockquote></article>@endforeach
    @if (in_array($ticket->status, ['open', 'in_progress', 'waiting_client'], true))
        <form class="portal-form-card" method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">@csrf<label>Răspuns către client<textarea name="message" rows="5" maxlength="4000" required></textarea></label><button class="button button-primary">Trimite răspunsul</button></form>
    @endif
</div>
@endsection
