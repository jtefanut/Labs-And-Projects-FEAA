<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Notifications\PortalUpdateNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $client = $request->user()->client;

        return view('client.dashboard', [
            'client' => $client,
            'servicesCount' => $client->services()->where('status', 'active')->count(),
            'openTicketsCount' => $client->tickets()->whereIn('status', ['open', 'in_progress'])->count(),
            'unpaidTotal' => $client->invoices()->whereIn('status', ['unpaid', 'overdue'])->sum('total'),
            'orders' => $client->orders()->latest()->limit(5)->get(),
            'products' => Product::where('is_active', true)->orderBy('sort_order')->get(),
            'notifications' => $request->user()->notifications()->latest()->limit(5)->get(),
        ]);
    }

    public function services(Request $request): View
    {
        return view('client.services', ['services' => $request->user()->client->services()->with('product')->latest()->get()]);
    }

    public function orders(Request $request): View
    {
        return view('client.orders', [
            'orders' => $request->user()->client->orders()->with('items.product', 'invoice')->latest()->get(),
            'products' => Product::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function createOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'terms_accepted' => ['accepted'],
        ]);
        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        $client = $request->user()->client;

        DB::transaction(function () use ($product, $client, $request) {
            $amount = $product->price;
            $order = Order::create([
                'order_number' => 'EH-'.now()->format('ymd').'-'.strtoupper(str()->random(5)),
                'client_id' => $client->id, 'status' => 'pending', 'subtotal' => $amount, 'tax' => '0.00', 'total' => $amount, 'currency' => 'RON',
            ]);
            OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 1, 'unit_price' => $amount, 'line_total' => $amount]);
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Order placed',
                'entity_type' => 'order',
                'entity_id' => $order->id,
                'metadata' => ['terms_accepted' => true, 'terms_version' => '2026-10-30'],
                'ip_address' => $request->ip(),
            ]);
        });

        return to_route('client.orders')->with('success', 'Comanda a fost înregistrată și așteaptă aprobarea administratorului.');
    }

    public function invoices(Request $request): View
    {
        return view('client.invoices', ['invoices' => $request->user()->client->invoices()->with('order', 'items')->latest()->get()]);
    }

    public function simulatePayment(Request $request, int $invoiceId): RedirectResponse
    {
        $invoice = $request->user()->client->invoices()->whereKey($invoiceId)->firstOrFail();
        abort_unless(in_array($invoice->status, ['unpaid', 'overdue'], true), 422, 'Factura nu poate fi plătită în starea curentă.');

        DB::transaction(function () use ($invoice, $request) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            abort_unless(in_array($invoice->status, ['unpaid', 'overdue'], true), 422, 'Factura a fost deja procesată.');

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'reference' => 'SIM-'.strtoupper(str()->random(12)),
                'amount' => $invoice->total,
                'method' => 'simulation',
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            $request->user()->notify(new PortalUpdateNotification(
                'Plata demo înregistrată',
                'Factura '.$invoice->invoice_number.' a fost marcată ca plătită în mediul demonstrativ.',
                route('client.invoices'),
            ));
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Simulated invoice payment',
                'entity_type' => 'invoice',
                'entity_id' => $invoice->id,
                'metadata' => ['payment_id' => $payment->id, 'amount' => $payment->amount],
                'ip_address' => $request->ip(),
            ]);
        });

        return to_route('client.invoices')->with('success', 'Plata demonstrativă a fost înregistrată. Nu a fost transferată nicio sumă reală.');
    }

    public function tickets(Request $request): View
    {
        return view('client.tickets', [
            'tickets' => $request->user()->client->tickets()->with('messages.user', 'service.product')->latest()->get(),
            'services' => $request->user()->client->services()->with('product')->latest()->get(),
        ]);
    }

    public function createTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:4000'],
            'priority' => ['required', 'in:low,normal,high'],
            'category' => ['required', 'in:general,billing,technical,domain'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ]);

        if (! empty($validated['service_id'])) {
            $request->user()->client->services()->findOrFail($validated['service_id']);
        }

        DB::transaction(function () use ($validated, $request) {
            $ticket = SupportTicket::create([
                'ticket_number' => 'TKT-'.strtoupper(str()->random(8)),
                'client_id' => $request->user()->client->id,
                'service_id' => $validated['service_id'] ?? null,
                'subject' => $validated['subject'], 'category' => $validated['category'],
                'priority' => $validated['priority'], 'status' => 'open',
            ]);
            TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $request->user()->id, 'body' => $validated['message']]);
            AuditLog::create(['user_id' => $request->user()->id, 'action' => 'Support ticket opened', 'entity_type' => 'ticket', 'entity_id' => $ticket->id, 'ip_address' => $request->ip()]);
        });

        return to_route('client.tickets')->with('success', 'Tichetul a fost trimis către echipa de suport.');
    }

    public function replyToTicket(Request $request, int $ticketId): RedirectResponse
    {
        $validated = $request->validate(['message' => ['required', 'string', 'max:4000']]);
        $ticket = $request->user()->client->tickets()->whereKey($ticketId)->firstOrFail();
        abort_unless(in_array($ticket->status, ['open', 'in_progress', 'waiting_client'], true), 422, 'Acest tichet este închis.');

        DB::transaction(function () use ($validated, $request, $ticket) {
            $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $validated['message']]);
            $ticket->update(['status' => 'open']);
            AuditLog::create(['user_id' => $request->user()->id, 'action' => 'Client replied to support ticket', 'entity_type' => 'ticket', 'entity_id' => $ticket->id, 'ip_address' => $request->ip()]);
        });

        return to_route('client.tickets')->with('success', 'Răspunsul a fost adăugat la tichet.');
    }

    public function profile(Request $request): View
    {
        return view('client.profile', ['client' => $request->user()->client]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:180', 'unique:users,email,'.$request->user()->id],
            'company' => ['nullable', 'string', 'max:160'], 'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:180'], 'city' => ['nullable', 'string', 'max:100'],
        ]);
        $request->user()->update(collect($validated)->only(['name', 'email'])->all());
        $request->user()->client->update(collect($validated)->except(['name', 'email'])->all());
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Client profile updated',
            'entity_type' => 'client',
            'entity_id' => $request->user()->client->id,
            'ip_address' => $request->ip(),
        ]);

        return to_route('client.profile')->with('success', 'Datele contului au fost actualizate.');
    }
}
