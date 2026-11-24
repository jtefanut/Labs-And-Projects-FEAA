@extends('layouts.portal')
@section('title', 'Facturi')
@section('content')
<div class="portal-welcome"><div><h2>Facturi demonstrative</h2><p>Aceste documente sunt folosite pentru demonstrarea fluxului. Nu sunt facturi fiscale.</p></div></div>
<div class="data-table-wrap"><table class="data-table"><thead><tr><th>Factură</th><th>Comandă</th><th>Emisă</th><th>Scadență</th><th>Total</th><th>Stare</th><th>Acțiune</th></tr></thead><tbody>
@forelse ($invoices as $invoice)
<tr>
    <td class="table-strong"><details><summary>{{ $invoice->invoice_number }}</summary>@foreach ($invoice->items as $item)<p>{{ $item->description }} × {{ $item->quantity }} · @money($item->line_total)</p>@endforeach</details></td><td>{{ $invoice->order?->order_number ?? '—' }}</td><td>{{ $invoice->created_at->format('d.m.Y') }}</td><td>{{ $invoice->due_at?->format('d.m.Y') ?? '—' }}</td><td>@money($invoice->total)</td>
    <td><span class="status-pill status-{{ $invoice->status }}">{{ $invoice->status }}</span></td>
    <td>@if (in_array($invoice->status, ['unpaid', 'overdue'], true))<form method="POST" action="{{ route('client.payments.store', $invoice->id) }}">@csrf<button class="button button-primary">Plătește demo</button></form>@else<span>—</span>@endif</td>
</tr>
@empty<tr><td colspan="7" class="table-empty">Nu există facturi pentru contul tău.</td></tr>@endforelse
</tbody></table></div>
<p class="portal-hint">Plata demo înregistrează o tranzacție fictivă în MySQL. Nu se transferă bani și nu se colectează date de card.</p>
@endsection
