@extends('layouts.portal')
@section('title', 'Serviciile mele')
@section('content')
<div class="portal-welcome"><div><h2>Serviciile mele</h2><p>Starea serviciilor contractate și următoarele date de reînnoire.</p></div><a class="portal-action" href="{{ route('client.orders') }}">Adaugă un serviciu ↗</a></div>
<p class="portal-hint">VPS-urile și hostname-urile din MVP sunt înregistrări simulate, nu mașini virtuale reale.</p>
<div class="data-table-wrap"><table class="data-table"><thead><tr><th>Serviciu</th><th>Plan și resurse</th><th>Hostname demo</th><th>Activat</th><th>Reînnoire</th><th>Stare</th></tr></thead><tbody>@forelse($services as $service)<tr><td class="table-strong">{{ $service->service_number }}</td><td>{{ $service->product->localizedName() }} @if($service->product->cpu_cores)<br>{{ $service->product->cpu_cores }} vCPU · {{ $service->product->ram_mb }} MB RAM · {{ $service->product->storage_gb }} GB @endif</td><td>{{ $service->hostname ?? '—' }}</td><td>{{ $service->starts_at?->format('d.m.Y') ?? '—' }}</td><td>{{ $service->renews_at?->format('d.m.Y') ?? '—' }}</td><td><span class="status-pill status-{{ $service->status }}">{{ $service->status }}</span></td></tr>@empty<tr><td colspan="6" class="table-empty">Nu există servicii contractate în acest cont.</td></tr>@endforelse</tbody></table></div>
@endsection
