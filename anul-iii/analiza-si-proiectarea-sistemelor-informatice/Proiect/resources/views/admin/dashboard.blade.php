@extends('layouts.portal')
@section('title', __('messages.portal.dashboard'))
@section('content')
<div class="admin-heading"><div><h2>{{ __('messages.admin.welcome', ['name' => auth()->user()->name]) }}</h2><p>{{ __('messages.admin.intro') }}</p></div><a class="button button-primary" href="{{ route('home') }}">{{ __('messages.admin.view_site') }} <span>↗</span></a></div>
<div class="metric-grid">
    @foreach($stats as $stat)<article><span>{{ $stat['label'] }}</span><strong>{{ $stat['value'] }}</strong></article>@endforeach
</div>
<div class="admin-dashboard-grid">
    <section class="panel-card admin-data-card">
        <div class="panel-card-heading"><h3>{{ __('messages.admin.recent_orders') }}</h3><a href="{{ route('admin.section', 'comenzi') }}">{{ __('messages.admin.all_orders') }} ↗</a></div>
        <div class="admin-order-list">
            @forelse($orders as $order)
                <article><div><strong>{{ $order->client->user->name }}</strong><span>{{ $order->items->map(fn($item) => $item->product?->localizedName() ?? '')->filter()->join(', ') ?: $order->order_number }}</span></div><div><b>{{ number_format((float)$order->total, 2, app()->getLocale() === 'ro' ? ',' : '.', app()->getLocale() === 'ro' ? '.' : ',') }} RON</b><span class="status-pill status-{{ $order->status }}">{{ $order->status }}</span></div></article>
            @empty<p class="table-empty">{{ __('messages.admin.empty_orders') }}</p>@endforelse
        </div>
    </section>
    <section class="panel-card admin-data-card">
        <div class="panel-card-heading"><h3>{{ __('messages.admin.open_requests') }}</h3><a href="{{ route('admin.section', 'tichete') }}">{{ __('messages.admin.all_tickets') }} ↗</a></div>
        <div class="admin-order-list">
            @forelse($tickets as $ticket)
                <article><div><strong>{{ $ticket->subject }}</strong><span>{{ $ticket->client->user->name }} · {{ $ticket->ticket_number }}</span></div><span class="status-pill status-{{ $ticket->status }}">{{ $ticket->status }}</span></article>
            @empty<p class="table-empty">{{ __('messages.admin.empty_tickets') }}</p>@endforelse
        </div>
    </section>
</div>
<div class="admin-financial panel-card"><div><span>{{ __('messages.admin.collected') }}</span><strong>{{ number_format((float)$revenue, 2, app()->getLocale() === 'ro' ? ',' : '.', app()->getLocale() === 'ro' ? '.' : ',') }} <small>RON</small></strong></div><div><span>{{ __('messages.admin.unpaid') }}</span><strong>{{ number_format((float)$outstanding, 2, app()->getLocale() === 'ro' ? ',' : '.', app()->getLocale() === 'ro' ? '.' : ',') }} <small>RON</small></strong></div><a href="{{ route('admin.section', 'facturi') }}">{{ __('messages.admin.financial_summary') }} ↗</a></div>
@endsection
