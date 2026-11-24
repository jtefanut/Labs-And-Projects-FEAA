@extends('layouts.portal')
@section('title', 'Rapoarte')
@section('content')
<div class="portal-welcome"><div><h2>Rapoarte economice și operaționale</h2><p>Valorile sunt calculate din datele din MySQL. Încasările și facturile sunt demonstrative.</p></div></div>
<form class="portal-form-card" method="GET" action="{{ route('admin.reports') }}">
    <div class="form-grid"><label>De la<input type="date" name="from" value="{{ $filters['from'] }}"></label><label>Până la<input type="date" name="to" value="{{ $filters['to'] }}"></label><label>Stare<select name="status"><option value="all" @selected($filters['status'] === 'all')>Toate</option>@foreach (['pending', 'processing', 'completed', 'cancelled', 'unpaid', 'paid', 'overdue'] as $status)<option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>@endforeach</select></label></div>
    <button class="button button-primary">Aplică filtrele</button>
    <a class="button" href="{{ route('admin.reports.export', request()->query()) }}">Exportă CSV ↗</a>
</form>
<div class="metric-grid">
    <article><span>Clienți</span><strong>{{ $clientCount }}</strong><small>Total conturi client</small></article>
    <article><span>Comenzi filtrate</span><strong>{{ $orderCount }}</strong><small>În intervalul selectat</small></article>
    <article><span>Servicii active</span><strong>{{ $activeServices }}</strong><small>Provisionări demonstrative</small></article>
    <article><span>Total facturi filtrate</span><strong>{{ number_format((float) $invoiceTotal, 2, ',', '.') }} RON</strong><small>Valoare simulată</small></article>
</div>
<h3>Comenzi</h3>
<div class="data-table-wrap"><table class="data-table"><thead><tr><th>Referință</th><th>Client</th><th>Data</th><th>Total</th><th>Stare</th></tr></thead><tbody>@forelse ($orders as $order)<tr><td>{{ $order->order_number }}</td><td>{{ $order->client->user->name }}</td><td>{{ $order->created_at->format('d.m.Y') }}</td><td>{{ number_format((float) $order->total, 2, ',', '.') }} RON</td><td>{{ $order->status }}</td></tr>@empty<tr><td colspan="5" class="table-empty">Nu există comenzi pentru filtrele selectate.</td></tr>@endforelse</tbody></table></div>
<h3>Facturi demonstrative</h3>
<div class="data-table-wrap"><table class="data-table"><thead><tr><th>Referință</th><th>Client</th><th>Data</th><th>Total</th><th>Stare</th></tr></thead><tbody>@forelse ($invoices as $invoice)<tr><td>{{ $invoice->invoice_number }}</td><td>{{ $invoice->client->user->name }}</td><td>{{ $invoice->created_at->format('d.m.Y') }}</td><td>{{ number_format((float) $invoice->total, 2, ',', '.') }} RON</td><td>{{ $invoice->status }}</td></tr>@empty<tr><td colspan="5" class="table-empty">Nu există facturi pentru filtrele selectate.</td></tr>@endforelse</tbody></table></div>
@endsection
