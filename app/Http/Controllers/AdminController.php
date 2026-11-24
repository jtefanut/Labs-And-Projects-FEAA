<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Server;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\PortalUpdateNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function payments(): View
    {
        return view('operator.payments', ['payments' => Payment::with('invoice.client.user')->latest()->paginate(25)]);
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => __('messages.admin.clients'), 'value' => Client::count()],
                ['label' => __('messages.admin.pending_orders'), 'value' => Order::whereIn('status', ['pending', 'processing'])->count()],
                ['label' => __('messages.admin.active_services'), 'value' => Service::where('status', 'active')->count()],
                ['label' => __('messages.admin.open_tickets'), 'value' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count()],
            ],
            'orders' => Order::with('client.user', 'items.product')->latest()->limit(6)->get(),
            'tickets' => SupportTicket::with('client.user')->whereIn('status', ['open', 'in_progress'])->latest()->limit(5)->get(),
            'revenue' => Invoice::where('status', 'paid')->sum('total'),
            'outstanding' => Invoice::whereIn('status', ['unpaid', 'overdue'])->sum('total'),
            'monthlyOrders' => $this->monthlyOrders(),
            'recentActivity' => AuditLog::with('user')->latest()->limit(6)->get(),
        ]);
    }

    public function index(string $section): View
    {
        $data = match ($section) {
            'clienti' => ['title' => 'Clienți', 'columns' => ['Client', 'Companie', 'E-mail', 'Țară', 'Înregistrat', 'Stare'], 'rows' => Client::with('user')->latest()->get()->map(fn ($r) => ['id' => $r->id, 'name' => $r->user->name, 'company' => $r->company ?: '—', 'email' => $r->user->email, 'country' => $r->country, 'created' => $r->created_at->format('d.m.Y'), 'status' => $r->user->is_active ? 'activ' : 'inactiv'])],
            'produse' => ['title' => 'Pachete și tarife', 'columns' => ['Produs', 'Categorie', 'Preț', 'Facturare', 'Stare'], 'rows' => Product::orderBy('category')->orderBy('sort_order')->get()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'category' => $r->category, 'price' => number_format((float) $r->price, 2).' RON', 'period' => $r->billing_period === 'yearly' ? 'Anual' : 'Lunar', 'status' => $r->is_active ? 'Activ' : 'Inactiv'])],
            'comenzi' => ['title' => 'Comenzi', 'columns' => ['Referință', 'Client', 'Total', 'Data', 'Stare'], 'rows' => Order::with('client.user')->latest()->get()->map(fn ($r) => ['id' => $r->id, 'number' => $r->order_number, 'client' => $r->client->user->name, 'total' => number_format((float) $r->total, 2).' RON', 'created' => $r->created_at->format('d.m.Y'), 'status' => $r->status])],
            'servicii' => ['title' => 'Servicii', 'columns' => ['Serviciu', 'Client', 'Produs', 'Reînnoire', 'Stare'], 'rows' => Service::with('client.user', 'product')->latest()->get()->map(fn ($r) => ['id' => $r->id, 'number' => $r->service_number, 'client' => $r->client->user->name, 'product' => $r->product->name, 'renews' => $r->renews_at?->format('d.m.Y') ?? '—', 'status' => $r->status])],
            'facturi' => ['title' => 'Facturi și plăți', 'columns' => ['Factură', 'Client', 'Total', 'Scadență', 'Stare'], 'rows' => Invoice::with('client.user')->latest()->get()->map(fn ($r) => ['id' => $r->id, 'number' => $r->invoice_number, 'client' => $r->client->user->name, 'total' => number_format((float) $r->total, 2).' RON', 'due' => $r->due_at?->format('d.m.Y') ?? '—', 'status' => $r->status])],
            'tichete' => ['title' => 'Tichete de suport', 'columns' => ['Tichet', 'Client', 'Subiect', 'Prioritate', 'Stare'], 'rows' => SupportTicket::with('client.user')->latest()->get()->map(fn ($r) => ['id' => $r->id, 'number' => $r->ticket_number, 'client' => $r->client->user->name, 'subject' => $r->subject, 'priority' => $r->priority, 'status' => $r->status])],
            'servere' => ['title' => 'Inventar servere', 'columns' => ['Nume', 'Hostname', 'Locație', 'Furnizor', 'Capacitate', 'Stare'], 'rows' => Server::latest()->get()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'hostname' => $r->hostname, 'location' => $r->location ?: '—', 'provider' => $r->provider ?: '—', 'capacity' => $r->capacity_percent.'%', 'status' => $r->status])],
            'furnizori' => ['title' => 'Furnizori și costuri', 'columns' => ['Furnizor', 'Serviciu', 'Contact', 'Cost lunar', 'Stare'], 'rows' => Supplier::latest()->get()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'service' => $r->service_type, 'email' => $r->contact_email ?: '—', 'cost' => number_format((float) $r->monthly_cost, 2).' RON', 'status' => $r->status])],
            'utilizatori' => ['title' => 'Utilizatori și roluri', 'columns' => ['Nume', 'E-mail', 'Rol', 'Creat', 'Stare'], 'rows' => User::with('role')->latest()->get()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'email' => $r->email, 'role' => $r->role?->label ?? 'Fără rol', 'created' => $r->created_at->format('d.m.Y'), 'status' => $r->is_active ? 'activ' : 'inactiv'])],
            'audit' => ['title' => 'Jurnalul operațiunilor', 'columns' => ['Acțiune', 'Utilizator', 'Entitate', 'Data'], 'rows' => AuditLog::with('user')->latest()->limit(100)->get()->map(fn ($r) => ['id' => $r->id, 'action' => $r->action, 'user' => $r->user?->name ?? 'Sistem', 'entity' => $r->entity_type ? $r->entity_type.' #'.$r->entity_id : '—', 'created' => $r->created_at->format('d.m.Y H:i')])],
            'rapoarte' => ['title' => 'Rapoarte economice și operaționale', 'columns' => ['Indicator', 'Valoare', 'Sursă'], 'rows' => collect([
                ['id' => 1, 'metric' => 'Venit încasat', 'value' => number_format((float) Invoice::where('status', 'paid')->sum('total'), 2).' RON', 'source' => 'Facturi marcate ca plătite'],
                ['id' => 2, 'metric' => 'Sold neîncasat', 'value' => number_format((float) Invoice::where('status', 'unpaid')->sum('total'), 2).' RON', 'source' => 'Facturi restante'],
                ['id' => 3, 'metric' => 'Cost furnizori / lună', 'value' => number_format((float) Supplier::where('status', 'active')->sum('monthly_cost'), 2).' RON', 'source' => 'Furnizori activi'],
                ['id' => 4, 'metric' => 'Utilizare medie servere', 'value' => (int) round(Server::avg('capacity_percent') ?? 0).'%', 'source' => 'Inventar servere'],
            ])],
            default => abort(404),
        };

        $data['title'] = __('messages.admin_sections.'.$section.'.title');
        $data['columns'] = __('messages.admin_sections.'.$section.'.columns');

        if ($section === 'produse') {
            $data['products'] = Product::orderBy('category')->orderBy('sort_order')->get();
        }

        return view('admin.list', ['section' => $section] + $data);
    }

    public function editClient(Client $client): View
    {
        $client->load('user');

        return view('admin.clients.edit', ['client' => $client]);
    }

    public function updateClient(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($client->user_id)],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'size:2'],
        ]);

        DB::transaction(function () use ($client, $request, $validated) {
            $client->user->update(['name' => $validated['name'], 'email' => $validated['email']]);
            $client->update(collect($validated)->except(['name', 'email'])->all());
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Client details updated',
                'entity_type' => 'client',
                'entity_id' => $client->id,
                'ip_address' => $request->ip(),
            ]);
        });

        return to_route('admin.clients.index')->with('success', 'Datele clientului au fost actualizate.');
    }

    public function updateStatus(Request $request, string $section, int $id): RedirectResponse
    {
        $configuration = [
            'produse' => [Product::class, ['activ', 'inactiv']],
            'clienti' => [Client::class, ['activ', 'inactiv']],
            'utilizatori' => [User::class, ['activ', 'inactiv']],
            'comenzi' => [Order::class, ['processing', 'cancelled', 'completed']],
            'servicii' => [Service::class, ['active', 'suspended', 'expired']],
            'facturi' => [Invoice::class, ['overdue', 'cancelled']],
            'tichete' => [SupportTicket::class, ['open', 'in_progress', 'waiting_client', 'resolved', 'closed']],
        ];
        abort_unless(isset($configuration[$section]), 404);
        [$model, $allowed] = $configuration[$section];
        $validated = $request->validate(['status' => ['required', 'in:'.implode(',', $allowed)]]);
        DB::transaction(function () use ($model, $id, $section, $validated, $request) {
            $record = $model::query()->lockForUpdate()->findOrFail($id);
            $previous = match ($section) {
                'produse' => $record->is_active ? 'activ' : 'inactiv',
                'clienti' => $record->user->is_active ? 'activ' : 'inactiv',
                'utilizatori' => $record->is_active ? 'activ' : 'inactiv',
                default => $record->status,
            };
            $this->assertStatusTransition($section, $previous, $validated['status']);

            match ($section) {
                'produse' => $record->update(['is_active' => $validated['status'] === 'activ']),
                'clienti' => $record->user->update(['is_active' => $validated['status'] === 'activ']),
                'utilizatori' => $record->update(['is_active' => $validated['status'] === 'activ']),
                default => $record->update(['status' => $validated['status']]),
            };

            if ($section === 'comenzi' && $validated['status'] === 'processing') {
                $this->provisionApprovedOrder($record);
            }

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Status changed',
                'entity_type' => $section,
                'entity_id' => $record->id,
                'metadata' => ['from' => $previous, 'to' => $validated['status']],
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Starea a fost actualizată.');
    }

    private function assertStatusTransition(string $section, string $from, string $to): void
    {
        $transitions = [
            'produse' => ['activ' => ['inactiv'], 'inactiv' => ['activ']],
            'clienti' => ['activ' => ['inactiv'], 'inactiv' => ['activ']],
            'utilizatori' => ['activ' => ['inactiv'], 'inactiv' => ['activ']],
            'comenzi' => ['pending' => ['processing', 'cancelled'], 'processing' => ['completed', 'cancelled']],
            'servicii' => ['pending' => ['active', 'expired'], 'active' => ['suspended', 'expired'], 'suspended' => ['active', 'expired']],
            'facturi' => ['unpaid' => ['overdue', 'cancelled'], 'overdue' => ['cancelled']],
            'tichete' => ['open' => ['in_progress', 'closed'], 'in_progress' => ['waiting_client', 'resolved', 'closed'], 'waiting_client' => ['in_progress', 'closed'], 'resolved' => ['closed']],
        ];

        abort_unless(in_array($to, $transitions[$section][$from] ?? [], true), 422, 'Tranziția de stare nu este permisă.');
    }

    private function provisionApprovedOrder(Order $order): void
    {
        $order->loadMissing('items.product');
        abort_if($order->items->isEmpty(), 422, 'Comanda nu conține produse.');
        abort_if($order->invoice()->exists() || $order->services()->exists(), 422, 'Comanda a fost deja provisionată.');

        $invoice = Invoice::create([
            'invoice_number' => 'INV-'.strtoupper(str()->random(10)),
            'client_id' => $order->client_id,
            'order_id' => $order->id,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'total' => $order->total,
            'status' => 'unpaid',
            'due_at' => now()->addDays(14)->toDateString(),
        ]);

        foreach ($order->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item->product_id,
                'description' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
            ]);
            $renewal = $item->product->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();
            Service::create([
                'client_id' => $order->client_id,
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'service_number' => 'SVC-'.strtoupper(str()->random(10)),
                'hostname' => $item->product->slug.'-'.strtolower(str()->random(6)).'.demo.eloquence.local',
                'status' => 'active',
                'starts_at' => today(),
                'renews_at' => $renewal->toDateString(),
                'expires_at' => $renewal->toDateString(),
            ]);
        }

        $order->client->user->notify(new PortalUpdateNotification(
            'Comanda a fost aprobată',
            'Serviciul demonstrativ și factura '.$invoice->invoice_number.' sunt disponibile în portal.',
            route('client.dashboard'),
        ));
    }

    public function createProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'], 'slug' => ['required', 'alpha_dash', 'max:120', 'unique:products,slug'],
            'category' => ['required', 'in:hosting,wordpress,vps,cloud,email,domain,backup,ssl,apps,dedicated,gpu,managed,storage,database,security,devops'], 'summary' => ['required', 'string', 'max:220'],
            'cpu_cores' => ['nullable', 'integer', 'min:1', 'max:128'], 'ram_mb' => ['nullable', 'integer', 'min:256', 'max:1048576'], 'storage_gb' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'], 'billing_period' => ['required', 'in:monthly,yearly'],
            'features' => ['required', 'string', 'max:1200'],
        ]);
        $validated['features'] = collect(preg_split('/\r\n|\r|\n/', $validated['features']))->map(fn ($line) => trim($line))->filter()->values()->all();
        $validated['is_active'] = true;
        Product::create($validated);
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'Product created', 'entity_type' => 'product']);

        return to_route('admin.section', 'produse')->with('success', 'Produsul a fost adăugat în catalog.');
    }

    public function reports(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', Rule::when($request->filled('from'), ['after_or_equal:from'])],
            'status' => ['nullable', 'in:all,pending,processing,completed,cancelled,unpaid,paid,overdue'],
        ]);
        $status = $filters['status'] ?? 'all';
        $ordersQuery = Order::with('client.user')->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))->when(in_array($status, ['pending', 'processing', 'completed', 'cancelled'], true), fn ($query) => $query->where('status', $status));
        $invoicesQuery = Invoice::with('client.user')->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))->when(in_array($status, ['unpaid', 'paid', 'overdue'], true), fn ($query) => $query->where('status', $status));

        return view('admin.reports', [
            'filters' => ['from' => $filters['from'] ?? '', 'to' => $filters['to'] ?? '', 'status' => $status],
            'orders' => $ordersQuery->latest()->limit(100)->get(),
            'invoices' => $invoicesQuery->latest()->limit(100)->get(),
            'orderCount' => (clone $ordersQuery)->count(),
            'invoiceTotal' => (clone $invoicesQuery)->sum('total'),
            'activeServices' => Service::where('status', 'active')->count(),
            'clientCount' => Client::count(),
        ]);
    }

    public function exportReports(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', Rule::when($request->filled('from'), ['after_or_equal:from'])],
            'status' => ['nullable', 'in:all,pending,processing,completed,cancelled,unpaid,paid,overdue'],
        ]);
        $status = $filters['status'] ?? 'all';

        return response()->streamDownload(function () use ($filters, $status) {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Tip', 'Referință', 'Client', 'Dată', 'Sumă RON', 'Stare']);
            $orders = Order::with('client.user')->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))->when(in_array($status, ['pending', 'processing', 'completed', 'cancelled'], true), fn ($query) => $query->where('status', $status))->orderBy('created_at')->cursor();
            foreach ($orders as $order) {
                fputcsv($stream, ['Comandă', $order->order_number, $order->client->user->name, $order->created_at->toDateString(), $order->total, $order->status]);
            }
            $invoices = Invoice::with('client.user')->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))->when(in_array($status, ['unpaid', 'paid', 'overdue'], true), fn ($query) => $query->where('status', $status))->orderBy('created_at')->cursor();
            foreach ($invoices as $invoice) {
                fputcsv($stream, ['Factură demonstrativă', $invoice->invoice_number, $invoice->client->user->name, $invoice->created_at->toDateString(), $invoice->total, $invoice->status]);
            }
            fclose($stream);
        }, 'eloquence-reports.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('products', 'slug')->ignore($product->id)],
            'category' => ['required', 'in:hosting,wordpress,vps,cloud,email,domain,backup,ssl,apps,dedicated,gpu,managed,storage,database,security,devops'],
            'summary' => ['required', 'string', 'max:220'], 'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'cpu_cores' => ['nullable', 'integer', 'min:1', 'max:128'], 'ram_mb' => ['nullable', 'integer', 'min:256', 'max:1048576'], 'storage_gb' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'billing_period' => ['required', 'in:monthly,yearly'], 'features' => ['required', 'string', 'max:1200'],
        ]);
        $validated['features'] = collect(preg_split('/\r\n|\r|\n/', $validated['features']))->map(fn ($line) => trim($line))->filter()->values()->all();
        $product->update($validated);
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'Product updated', 'entity_type' => 'product', 'entity_id' => $product->id]);

        return to_route('admin.section', 'produse')->with('success', 'Produsul a fost actualizat.');
    }

    private function monthlyOrders(): array
    {
        return collect(range(5, 0))->map(function (int $monthsAgo) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return ['label' => $month->translatedFormat('M'), 'value' => Order::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count()];
        })->all();
    }
}
