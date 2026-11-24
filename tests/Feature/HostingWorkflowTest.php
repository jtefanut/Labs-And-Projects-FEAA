<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class HostingWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_approval_creates_demo_service_and_invoice_using_order_price_snapshot(): void
    {
        $client = $this->clientUser('Client One', 'client-one@example.test');
        $admin = $this->adminUser();
        $product = $this->product('vps-demo', '89.00');

        $this->actingAs($client)
            ->post(route('client.orders.store'), ['product_id' => $product->id])
            ->assertRedirect(route('client.orders'));

        $order = Order::query()->firstOrFail();
        $product->update(['price' => '109.00']);

        $this->actingAs($admin)
            ->post(route('admin.status.update', ['comenzi', $order->id]), ['status' => 'processing'])
            ->assertRedirect();

        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'unit_price' => '89.00']);
        $this->assertDatabaseHas('services', ['order_id' => $order->id, 'client_id' => $client->client->id, 'status' => 'active']);
        $this->assertDatabaseHas('invoices', ['order_id' => $order->id, 'client_id' => $client->client->id, 'total' => '89.00', 'status' => 'unpaid']);
        $this->assertDatabaseHas('invoice_items', ['description' => 'vps-demo', 'unit_price' => '89.00', 'line_total' => '89.00']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'Status changed', 'entity_type' => 'comenzi']);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $client->id, 'data->title' => 'Comanda a fost aprobată']);
    }

    public function test_client_can_pay_own_demo_invoice_but_cannot_access_another_clients_invoice(): void
    {
        $owner = $this->clientUser('Invoice Owner', 'invoice-owner@example.test');
        $other = $this->clientUser('Other Client', 'other-client@example.test');
        $invoice = Invoice::create([
            'invoice_number' => 'INV-OWN-001', 'client_id' => $owner->client->id,
            'subtotal' => '55.00', 'tax' => '0.00', 'total' => '55.00', 'currency' => 'RON',
            'status' => 'unpaid', 'due_at' => now()->addDays(14)->toDateString(),
        ]);

        $this->actingAs($other)
            ->post(route('client.payments.store', $invoice->id))
            ->assertNotFound();

        $this->assertDatabaseCount('payments', 0);

        $this->actingAs($owner)
            ->post(route('client.payments.store', $invoice->id))
            ->assertRedirect(route('client.invoices'));

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => '55.00', 'method' => 'simulation', 'status' => 'completed']);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $owner->id, 'action' => 'Simulated invoice payment', 'entity_id' => $invoice->id]);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $owner->id, 'data->title' => 'Plata demo înregistrată']);

        $admin = $this->adminUser();
        $this->actingAs($admin)
            ->post(route('admin.status.update', ['facturi', $invoice->id]), ['status' => 'paid'])
            ->assertSessionHasErrors('status');
    }

    public function test_ticket_conversation_is_private_and_admin_and_client_can_reply(): void
    {
        $owner = $this->clientUser('Ticket Owner', 'ticket-owner@example.test');
        $other = $this->clientUser('Ticket Other', 'ticket-other@example.test');
        $admin = $this->adminUser();
        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-OWN-001', 'client_id' => $owner->client->id,
            'subject' => 'Mail configuration', 'category' => 'technical', 'priority' => 'high', 'status' => 'open',
        ]);

        $this->actingAs($other)
            ->post(route('client.tickets.reply', $ticket->id), ['message' => 'Can I see this?'])
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.tickets.reply', $ticket), ['message' => 'We are checking the mail settings.'])
            ->assertRedirect(route('admin.tickets.show', $ticket));

        $this->assertDatabaseHas('support_tickets', ['id' => $ticket->id, 'status' => 'waiting_client']);

        $this->actingAs($owner)
            ->post(route('client.tickets.reply', $ticket->id), ['message' => 'Thank you, I can test now.'])
            ->assertRedirect(route('client.tickets'));

        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'user_id' => $admin->id, 'body' => 'We are checking the mail settings.']);
        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'user_id' => $owner->id, 'body' => 'Thank you, I can test now.']);
        $this->assertSame(2, TicketMessage::query()->where('ticket_id', $ticket->id)->count());
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $owner->id, 'data->title' => 'Răspuns nou la tichet']);
    }

    public function test_role_middleware_keeps_client_and_admin_areas_separate(): void
    {
        $client = $this->clientUser('Client', 'role-client@example.test');
        $admin = $this->adminUser();

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->actingAs($client)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('client.dashboard'))->assertForbidden();
    }

    public function test_inactive_product_and_invalid_ticket_data_are_rejected_without_writes(): void
    {
        $client = $this->clientUser('Validating Client', 'validating@example.test');
        $inactiveProduct = $this->product('inactive-demo', '19.00', false);

        $this->actingAs($client)
            ->post(route('client.orders.store'), ['product_id' => $inactiveProduct->id])
            ->assertNotFound();

        $this->actingAs($client)
            ->post(route('client.tickets.store'), [
                'subject' => '', 'message' => '', 'category' => 'unlisted', 'priority' => 'urgent',
            ])
            ->assertSessionHasErrors(['subject', 'message', 'category', 'priority']);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('support_tickets', 0);
    }

    public function test_password_reset_changes_the_password_using_a_valid_token(): void
    {
        $client = $this->clientUser('Reset Client', 'reset-client@example.test');
        $token = Password::createToken($client);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $client->email,
            'password' => 'A-New-Strong-Password-123!',
            'password_confirmation' => 'A-New-Strong-Password-123!',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('A-New-Strong-Password-123!', $client->fresh()->password));
    }

    public function test_password_recovery_sends_a_reset_link_for_a_registered_account(): void
    {
        $client = $this->clientUser('Forgot Client', 'forgot-client@example.test');
        Notification::fake();

        $this->post(route('password.email'), ['email' => $client->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($client, ResetPassword::class);
    }

    public function test_deactivated_account_cannot_sign_in(): void
    {
        $client = $this->clientUser('Inactive Client', 'inactive-client@example.test');
        $client->update(['is_active' => false]);

        $this->post(route('login.store'), ['email' => $client->email, 'password' => 'password'])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_report_filters_rows_and_exports_csv(): void
    {
        $client = $this->clientUser('Report Client', 'report-client@example.test');
        $admin = $this->adminUser();
        $order = Order::create([
            'order_number' => 'EH-REPORT-001', 'client_id' => $client->client->id, 'status' => 'pending',
            'subtotal' => '39.00', 'tax' => '0.00', 'total' => '39.00', 'currency' => 'RON',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('EH-REPORT-001')
            ->assertDontSee('INV-ABSENT');

        $csv = $this->get(route('admin.reports.export', ['status' => 'pending']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString('EH-REPORT-001', $csv->streamedContent());
    }

    public function test_admin_can_edit_client_contact_data_and_the_change_is_audited(): void
    {
        $client = $this->clientUser('Editable Client', 'editable-client@example.test');
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->put(route('admin.clients.update', $client->client->id), [
                'name' => 'Updated Client',
                'email' => 'updated-client@example.test',
                'company' => 'Updated Studio',
                'phone' => '+40 700 000 000',
                'address' => 'Strada Exemplu 10',
                'city' => 'București',
                'country' => 'RO',
            ])
            ->assertRedirect(route('admin.clients.index'));

        $this->assertDatabaseHas('users', ['id' => $client->id, 'name' => 'Updated Client', 'email' => 'updated-client@example.test']);
        $this->assertDatabaseHas('clients', ['id' => $client->client->id, 'company' => 'Updated Studio', 'city' => 'București']);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $admin->id, 'action' => 'Client details updated']);
    }

    private function clientUser(string $name, string $email): User
    {
        $role = Role::firstOrCreate(['name' => 'client'], ['label' => 'Client']);
        $user = User::factory()->create(['name' => $name, 'email' => $email, 'role_id' => $role->id]);
        Client::create(['user_id' => $user->id, 'country' => 'RO']);

        return $user->fresh();
    }

    private function adminUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function product(string $name, string $price, bool $isActive = true): Product
    {
        return Product::create([
            'name' => $name, 'slug' => $name, 'category' => 'vps', 'summary' => 'Demo VPS',
            'price' => $price, 'billing_period' => 'monthly', 'features' => ['2 vCPU', '4 GB RAM'],
            'is_active' => $isActive,
        ]);
    }
}
