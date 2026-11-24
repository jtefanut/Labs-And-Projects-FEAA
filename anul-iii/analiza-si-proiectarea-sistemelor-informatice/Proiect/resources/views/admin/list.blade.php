@extends('layouts.portal')

@section('title', $title)

@section('content')
<div class="portal-welcome">
    <div>
        <h2>{{ $title }}</h2>
    </div>
    <span class="row-count">{{ __('messages.admin_list.records', ['count' => count($rows)]) }}</span>
</div>

@if ($section === 'produse')
    <details class="product-create">
        <summary>+ {{ __('messages.admin_list.add_product') }}</summary>
        <form method="POST" action="{{ route('admin.products.store.en') }}" class="product-editor">
            @csrf
            <div class="form-grid">
                <label>{{ __('messages.admin_list.name') }}<input name="name" required maxlength="100"></label>
                <label>{{ __('messages.admin_list.slug') }}<input name="slug" required maxlength="120"></label>
                <label>{{ __('messages.admin_list.category') }}<select name="category">@foreach (['hosting', 'wordpress', 'vps', 'cloud', 'domain', 'email', 'backup', 'ssl', 'apps', 'dedicated', 'gpu', 'managed', 'storage', 'database', 'security', 'devops'] as $key)<option value="{{ $key }}">{{ __('messages.catalog.categories.'.$key) }}</option>@endforeach</select></label>
                <label>{{ __('messages.admin_list.price_ron') }}<input type="number" min="0" step="0.01" name="price" required></label>
                <label>{{ __('messages.admin_list.billing') }}<select name="billing_period"><option value="monthly">{{ __('messages.admin_list.monthly') }}</option><option value="yearly">{{ __('messages.admin_list.yearly') }}</option></select></label>
                <label>{{ __('messages.admin_list.cpu') }}<input type="number" min="1" max="128" name="cpu_cores"></label>
                <label>{{ __('messages.admin_list.ram') }}<input type="number" min="256" name="ram_mb"></label>
                <label>{{ __('messages.admin_list.storage') }}<input type="number" min="1" name="storage_gb"></label>
                <label class="span-two">{{ __('messages.admin_list.summary') }}<input name="summary" required maxlength="220"></label>
                <label class="span-two">{{ __('messages.admin_list.features') }}<textarea name="features" rows="3" required></textarea></label>
            </div>
            <button class="button button-primary">{{ __('messages.admin_list.save_product') }} <span>↗</span></button>
        </form>
    </details>

    <div class="product-admin-grid">
        @foreach ($products as $product)
            <details class="product-edit-card">
                <summary>
                    <span><small>{{ strtoupper($product->category) }}</small><b>{{ $product->name }}</b></span>
                    <span>{{ number_format((float) $product->price, 2, ',', '.') }} RON <i class="status-pill status-{{ $product->is_active ? 'active' : 'cancelled' }}">{{ $product->is_active ? 'Activ' : 'Inactiv' }}</i></span>
                </summary>
                <form method="POST" action="{{ route('admin.products.update.en', $product) }}" class="product-editor">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <label>{{ __('messages.admin_list.name') }}<input name="name" value="{{ $product->name }}" required></label>
                        <label>Categorie
                            <select name="category">
                                @foreach (['hosting', 'wordpress', 'vps', 'cloud', 'domain', 'email', 'backup', 'ssl', 'apps', 'dedicated', 'gpu', 'managed', 'storage', 'database', 'security', 'devops'] as $key)
                                    <option value="{{ $key }}" @selected($product->category === $key)>{{ __('messages.catalog.categories.'.$key) }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>{{ __('messages.admin_list.slug') }}<input name="slug" value="{{ $product->slug }}" required></label>
                        <label>{{ __('messages.admin_list.price_ron') }}<input type="number" min="0" step="0.01" name="price" value="{{ $product->price }}" required></label>
                        <label>Facturare
                            <select name="billing_period">
                                <option value="monthly" @selected($product->billing_period === 'monthly')>{{ __('messages.admin_list.monthly') }}</option>
                                <option value="yearly" @selected($product->billing_period === 'yearly')>{{ __('messages.admin_list.yearly') }}</option>
                            </select>
                        </label>
                        <label>{{ __('messages.admin_list.cpu') }}<input type="number" min="1" max="128" name="cpu_cores" value="{{ $product->cpu_cores }}"></label>
                        <label>{{ __('messages.admin_list.ram') }}<input type="number" min="256" name="ram_mb" value="{{ $product->ram_mb }}"></label>
                        <label>{{ __('messages.admin_list.storage') }}<input type="number" min="1" name="storage_gb" value="{{ $product->storage_gb }}"></label>
                        <label class="span-two">{{ __('messages.admin_list.summary') }}<input name="summary" value="{{ $product->summary }}" required></label>
                        <label class="span-two">{{ __('messages.admin_list.features') }}<textarea name="features" rows="3" required>{{ implode("\n", $product->features ?? []) }}</textarea></label>
                    </div>
                    <button class="button button-primary">{{ __('messages.admin_list.update') }} <span>↗</span></button>
                </form>
                <form method="POST" action="{{ route('admin.status.update', ['produse', $product->id]) }}" class="status-form">
                    @csrf
                    <input type="hidden" name="status" value="{{ $product->is_active ? 'inactiv' : 'activ' }}">
                    <button>@if($product->is_active){{ __('messages.admin_list.deactivate') }}@else{{ __('messages.admin_list.activate') }}@endif</button>
                </form>
            </details>
        @endforeach
    </div>
@else
    <div class="data-table-wrap">
        <table class="data-table admin-table">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                    @if (in_array($section, ['clienti', 'utilizatori', 'comenzi', 'servicii', 'facturi', 'tichete']))
                        <th>{{ __('messages.admin_list.update') }}</th>
                    @endif
                    @if ($section === 'tichete')<th>{{ __('messages.admin_list.conversation') }}</th>@endif
                    @if ($section === 'clienti')<th>{{ __('messages.admin_list.client_details') }}</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @php($values = collect($row)->except('id')->values())
                        @foreach ($values as $index => $value)
                            <td class="{{ $index === 0 ? 'table-strong' : '' }}">@php($statusKey = 'messages.admin_list.statuses.'.$value){{ __($statusKey) === $statusKey ? $value : __($statusKey) }}</td>
                        @endforeach
                        @if (in_array($section, ['clienti', 'utilizatori', 'comenzi', 'servicii', 'facturi', 'tichete']))
                            <td>
                                @php($options = match ($section) {
                                    'clienti', 'utilizatori' => ['activ' => ['inactiv'], 'inactiv' => ['activ']],
                                    'comenzi' => ['pending' => ['processing', 'cancelled'], 'processing' => ['completed', 'cancelled']],
                                    'servicii' => ['pending' => ['active', 'expired'], 'active' => ['suspended', 'expired'], 'suspended' => ['active', 'expired']],
                                    'facturi' => ['unpaid' => ['overdue', 'cancelled'], 'overdue' => ['cancelled']],
                                    'tichete' => ['open' => ['in_progress', 'closed'], 'in_progress' => ['waiting_client', 'resolved', 'closed'], 'waiting_client' => ['in_progress', 'closed'], 'resolved' => ['closed']],
                                })
                                @if (!empty($options[$row['status']]))
                                    <form method="POST" action="{{ route('admin.status.update', [$section, $row['id']]) }}" class="status-form">
                                        @csrf
                                        <select name="status">
                                            @foreach ($options[$row['status']] as $option)@php($optionKey = 'messages.admin_list.statuses.'.$option)<option value="{{ $option }}">{{ __($optionKey) === $optionKey ? $option : __($optionKey) }}</option>@endforeach
                                        </select>
                                        <button aria-label="{{ __('messages.admin_list.save_status') }}">↗</button>
                                    </form>
                                @else<span>—</span>@endif
                            </td>
                        @endif
                        @if ($section === 'tichete')<td><a href="{{ route('admin.tickets.show', $row['id']) }}">{{ __('messages.admin_list.open_conversation') }} ↗</a></td>@endif
                        @if ($section === 'clienti')<td><a href="{{ route('admin.clients.edit', $row['id']) }}">{{ __('messages.admin_list.edit') }} ↗</a></td>@endif
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + (in_array($section, ['clienti', 'utilizatori', 'comenzi', 'servicii', 'facturi', 'tichete']) ? 1 : 0) + ($section === 'tichete' ? 1 : 0) + ($section === 'clienti' ? 1 : 0) }}" class="table-empty">{{ __('messages.admin_list.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection
