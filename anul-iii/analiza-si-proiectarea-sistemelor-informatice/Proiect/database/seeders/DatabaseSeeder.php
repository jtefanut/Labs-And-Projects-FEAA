<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\Server;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $clientRole = Role::firstOrCreate(['name' => 'client'], ['label' => 'Client']);
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $paymentRole = Role::firstOrCreate(['name' => 'payment_operator'], ['label' => 'Payment operator']);

        $admins = collect([
            ['Moana', 'moana@eloquence.local'],
            ['Stefanut', 'stefanut@eloquence.local'],
            ['Stanciu Laurentiu', 'stanciu.laurentiu@eloquence.local'],
        ])->map(fn (array $admin) => User::updateOrCreate(['email' => $admin[1]], [
            'name' => $admin[0],
            'password' => Hash::make(env('DEMO_PASSWORD') ?: Str::random(48)),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]));

        $eloquenceUser = User::updateOrCreate(['email' => 'eloquence.user@demo.eloquence.local'], [
            'name' => 'Eloquence User',
            'password' => Hash::make(env('DEMO_PASSWORD') ?: Str::random(48)),
            'role_id' => $clientRole->id,
            'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'payments@eloquence.local'], [
            'name' => 'Payment Operator',
            'password' => Hash::make(env('DEMO_PASSWORD') ?: Str::random(48)),
            'role_id' => $paymentRole->id,
            'is_active' => true,
        ]);
        $stanciu = $admins->firstWhere('email', 'stanciu.laurentiu@eloquence.local');
        $clientUsers = collect([$eloquenceUser, $stanciu]);

        Client::query()->whereNotIn('user_id', $clientUsers->pluck('id'))->delete();
        Client::updateOrCreate(['user_id' => $eloquenceUser->id], ['company' => 'Eloquence User', 'country' => 'RO']);
        Client::updateOrCreate(['user_id' => $stanciu->id], ['company' => 'Stanciu Laurentiu', 'country' => 'RO']);

        User::query()->whereIn('email', [
            'admin@eloquence.local',
            'mara@demo.eloquence.local',
            'andrei@demo.eloquence.local',
            'ioana@demo.eloquence.local',
            'radu@demo.eloquence.local',
        ])->delete();

        foreach ([
            'launch' => 'web-start',
            'scale' => 'web-growth',
            'studio' => 'web-agency',
            'email-pro' => 'business-email',
        ] as $oldSlug => $newSlug) {
            Product::where('slug', $oldSlug)->update(['slug' => $newSlug]);
        }

        $products = [
            ['name' => 'Web Start', 'slug' => 'web-start', 'category' => 'hosting', 'summary' => 'Găzduire pentru un site de prezentare sau proiect la început.', 'price' => 91.95, 'billing_period' => 'monthly', 'features' => ['25 GB NVMe', '1 website', '5 căsuțe e-mail', 'SSL și backup automat'], 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Web Growth', 'slug' => 'web-growth', 'category' => 'hosting', 'summary' => 'Mai multe resurse pentru site-uri și magazine în creștere.', 'price' => 183.95, 'billing_period' => 'monthly', 'features' => ['75 GB NVMe', '10 website-uri', '25 căsuțe e-mail', 'Backup zilnic și migrare asistată'], 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Web Agency', 'slug' => 'web-agency', 'category' => 'hosting', 'summary' => 'Un spațiu de lucru pentru portofolii și proiecte multiple.', 'price' => 367.95, 'billing_period' => 'monthly', 'features' => ['150 GB NVMe', '30 website-uri', '100 căsuțe e-mail', 'Izolare proiecte și suport prioritar'], 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Managed WordPress', 'slug' => 'managed-wordpress', 'category' => 'wordpress', 'summary' => 'WordPress administrat, cu actualizări și copii de siguranță.', 'price' => 68.95, 'billing_period' => 'monthly', 'features' => ['Instalare WordPress', 'Actualizări asistate', 'Backup zilnic', 'Staging pentru modificări'], 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'VPS Start', 'slug' => 'vps-start', 'category' => 'vps', 'summary' => 'Un server virtual pentru aplicații care au nevoie de acces root.', 'cpu_cores' => 2, 'ram_mb' => 4096, 'storage_gb' => 80, 'price' => 41.35, 'billing_period' => 'monthly', 'features' => ['2 vCPU', '4 GB RAM', '80 GB NVMe', 'Acces root și reinstalare OS'], 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'VPS Business', 'slug' => 'vps-business', 'category' => 'vps', 'summary' => 'Resurse dedicate pentru aplicații și medii de producție.', 'cpu_cores' => 4, 'ram_mb' => 8192, 'storage_gb' => 160, 'price' => 91.95, 'billing_period' => 'monthly', 'features' => ['4 vCPU', '8 GB RAM', '160 GB NVMe', 'Snapshot-uri și IP dedicat'], 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'VPS Compute', 'slug' => 'vps-compute', 'category' => 'vps', 'summary' => 'Mai mult CPU și memorie pentru sarcini intense.', 'cpu_cores' => 8, 'ram_mb' => 16384, 'storage_gb' => 320, 'price' => 229.95, 'billing_period' => 'monthly', 'features' => ['8 vCPU', '16 GB RAM', '320 GB NVMe', 'Trafic generos și snapshots'], 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Cloud Launch', 'slug' => 'cloud-launch', 'category' => 'cloud', 'summary' => 'Resurse scalabile pentru aplicații cu trafic variabil.', 'cpu_cores' => 4, 'ram_mb' => 8192, 'storage_gb' => 160, 'price' => 137.95, 'billing_period' => 'monthly', 'features' => ['4 vCPU', '8 GB RAM', '160 GB NVMe', 'Scalare verticală și backup zilnic'], 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Cloud Scale', 'slug' => 'cloud-scale', 'category' => 'cloud', 'summary' => 'Capacitate extinsă pentru magazine și servicii cu utilizare ridicată.', 'cpu_cores' => 8, 'ram_mb' => 32768, 'storage_gb' => 500, 'price' => 275.95, 'billing_period' => 'monthly', 'features' => ['8 vCPU', '32 GB RAM', '500 GB NVMe', 'Load balancing și snapshot-uri'], 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Business Email', 'slug' => 'business-email', 'category' => 'email', 'summary' => 'Adrese profesionale pentru echipa ta și domeniul propriu.', 'price' => 18.35, 'billing_period' => 'monthly', 'features' => ['10 căsuțe poștale', '30 GB spațiu', 'Filtrare spam', 'IMAP și webmail'], 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Domeniu .ro', 'slug' => 'domain-ro', 'category' => 'domain', 'summary' => 'Înregistrează domeniul și administrează setările DNS.', 'price' => 59.75, 'billing_period' => 'yearly', 'features' => ['Administrare DNS', 'Reînnoire anuală', 'Asistență la transfer'], 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Backup Vault', 'slug' => 'backup-vault', 'category' => 'backup', 'summary' => 'Păstrează copii de siguranță independente pentru proiectele tale.', 'price' => 22.95, 'billing_period' => 'monthly', 'features' => ['100 GB spațiu backup', 'Păstrare 30 zile', 'Restaurare la cerere', 'Criptare în tranzit'], 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'SSL Certificate', 'slug' => 'ssl-certificate', 'category' => 'ssl', 'summary' => 'Certificat TLS pentru conexiuni HTTPS la domeniul tău.', 'price' => 13.75, 'billing_period' => 'yearly', 'features' => ['Validare domeniu', 'Instalare asistată', 'Reînnoire anuală', 'Compatibilitate browser'], 'is_featured' => false, 'sort_order' => 1],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product + ['is_active' => true]);
        }

        $this->call(CatalogExtensionsSeeder::class);

        foreach ($clientUsers as $clientIndex => $user) {
            $client = $user->client;
            $clientProducts = Product::whereIn('slug', $clientIndex === 0 ? ['web-growth', 'domain-ro'] : ['cloud-launch', 'business-email'])->get();

            foreach ($clientProducts as $productIndex => $product) {
                $order = Order::firstOrCreate(['order_number' => 'EH-2026-'.str_pad((string) ($clientIndex * 2 + $productIndex + 1), 4, '0', STR_PAD_LEFT)], [
                    'client_id' => $client->id, 'status' => $productIndex === 0 ? 'processing' : 'pending',
                    'subtotal' => $product->price, 'tax' => 0, 'total' => $product->price,
                ]);
                OrderItem::firstOrCreate(['order_id' => $order->id, 'product_id' => $product->id], ['quantity' => 1, 'unit_price' => $product->price, 'line_total' => $product->price]);
                $invoice = Invoice::firstOrCreate(['invoice_number' => 'INV-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)], [
                    'client_id' => $client->id, 'order_id' => $order->id, 'subtotal' => $product->price,
                    'tax' => 0, 'total' => $product->price, 'status' => $productIndex === 0 ? 'unpaid' : 'paid',
                    'due_at' => now()->addDays(14)->toDateString(), 'paid_at' => $productIndex === 0 ? null : now(),
                ]);
                InvoiceItem::firstOrCreate(['invoice_id' => $invoice->id], ['product_id' => $product->id, 'description' => $product->name, 'quantity' => 1, 'unit_price' => $product->price, 'line_total' => $product->price]);
                if ($invoice->status === 'paid') {
                    Payment::firstOrCreate(['invoice_id' => $invoice->id], ['reference' => 'SIM-DEMO-'.$invoice->id, 'amount' => $invoice->total, 'method' => 'simulation', 'status' => 'completed', 'paid_at' => $invoice->paid_at ?? now()]);
                }
                if ($productIndex === 0) {
                    Service::updateOrCreate(['service_number' => 'SVC-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT)], [
                        'client_id' => $client->id, 'product_id' => $product->id, 'order_id' => $order->id,
                        'hostname' => $product->slug.'-'.str_pad((string) $order->id, 3, '0', STR_PAD_LEFT).'.demo.eloquence.local',
                        'status' => 'active', 'starts_at' => now()->subDays(24)->toDateString(), 'renews_at' => now()->addDays(6)->toDateString(), 'expires_at' => now()->addDays(6)->toDateString(),
                    ]);
                }
            }
        }

        foreach ($clientUsers as $index => $user) {
            $client = $user->client;
            $ticket = SupportTicket::firstOrCreate(['ticket_number' => 'TKT-'.str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT)], [
                'client_id' => $client->id,
                'subject' => $index === 0 ? 'Migrare website și domeniu' : 'Configurare mediu cloud',
                'priority' => 'normal', 'status' => $index === 0 ? 'open' : 'in_progress',
            ]);
            TicketMessage::firstOrCreate(['ticket_id' => $ticket->id, 'user_id' => $user->id], ['body' => 'Bună, am nevoie de ajutor cu următorul pas pentru serviciul meu.']);
        }

        Server::firstOrCreate(['hostname' => 'node-ro-01.eloquence.local'], ['name' => 'RO Edge 01', 'location' => 'București, RO', 'provider' => 'Demo infrastructure', 'status' => 'planned', 'capacity_percent' => 42]);
        Server::firstOrCreate(['hostname' => 'backup-eu-01.eloquence.local'], ['name' => 'EU Backup 01', 'location' => 'EU', 'provider' => 'Demo infrastructure', 'status' => 'planned', 'capacity_percent' => 18]);
        Supplier::firstOrCreate(['name' => 'Eloquence Infrastructure Demo'], ['service_type' => 'Compute & storage', 'contact_email' => 'ops@eloquence.local', 'monthly_cost' => 1250, 'status' => 'active']);
        AuditLog::firstOrCreate(['action' => 'Demo workspace initialized'], ['user_id' => $admins->first()->id, 'entity_type' => 'system', 'metadata' => ['environment' => 'local demo']]);
    }
}
