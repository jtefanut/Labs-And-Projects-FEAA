@extends('layouts.portal')

@section('title', __('messages.operator.title'))

@section('content')
<div class="portal-welcome"><div><h2>{{ __('messages.operator.title') }}</h2><p>{{ __('messages.operator.description') }}</p></div><span class="row-count">{{ $payments->total() }} {{ __('messages.operator.records') }}</span></div>
<div class="data-table-wrap"><table class="data-table admin-table"><thead><tr><th>{{ __('messages.operator.reference') }}</th><th>{{ __('messages.operator.client') }}</th><th>{{ __('messages.operator.invoice') }}</th><th>{{ __('messages.operator.amount') }}</th><th>{{ __('messages.operator.method') }}</th><th>{{ __('messages.operator.status') }}</th><th>{{ __('messages.operator.date') }}</th></tr></thead><tbody>
@forelse($payments as $payment)
<tr><td class="table-strong">{{ $payment->reference ?? '—' }}</td><td>{{ $payment->invoice->client->user->name }}</td><td>{{ $payment->invoice->invoice_number }}</td><td>{{ number_format((float) $payment->amount, 2, ',', '.') }} RON</td><td>{{ $payment->method ?? '—' }}</td><td><span class="status-pill status-{{ $payment->status }}">{{ __('messages.admin_list.statuses.'.$payment->status) }}</span></td><td>{{ $payment->paid_at?->format('d.m.Y H:i') ?? '—' }}</td></tr>
@empty
<tr><td colspan="7">{{ __('messages.admin_list.empty') }}</td></tr>
@endforelse
</tbody></table></div>
<div class="pagination-wrap">{{ $payments->links() }}</div>
@endsection
