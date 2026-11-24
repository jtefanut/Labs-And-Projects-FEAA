<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CatalogExtensionsSeeder extends Seeder
{
    public function run(): void
    {
        $paymentRole = Role::firstOrCreate(['name' => 'payment_operator'], ['label' => 'Payment operator']);
        User::updateOrCreate(['email' => 'payments@eloquence.local'], [
            'name' => 'Payment Operator',
            'password' => Hash::make(env('DEMO_PASSWORD') ?: Str::random(48)),
            'role_id' => $paymentRole->id,
            'is_active' => true,
        ]);

        $additionalProducts = [
            ['shared-hosting', 'hosting', 'Shared Hosting', 'Shared hosting for business websites and small projects.', 19.99, ['SSD storage', 'SSL certificate', 'Daily backup']],
            ['laravel-php-hosting', 'apps', 'Laravel & PHP Hosting', 'A PHP environment configured for Laravel applications.', 34.99, ['Supported PHP versions', 'Composer workflow', 'Managed TLS']],
            ['node-app-hosting', 'apps', 'Node.js Application Hosting', 'Managed runtime for Node.js applications and APIs.', 39.99, ['Node.js runtime', 'Process monitoring', 'Git deployment']],
            ['static-site-hosting', 'apps', 'Static Website Hosting', 'Fast delivery for static sites and documentation.', 9.99, ['CDN delivery', 'TLS included', 'Git publishing']],
            ['reseller-hosting', 'hosting', 'Reseller Hosting', 'Hosting resources for agencies managing multiple clients.', 79.99, ['Separate accounts', 'Resource limits', 'Client-ready management']],
            ['wordpress-business', 'wordpress', 'WordPress Business', 'WordPress hosting with staging and managed updates.', 29.99, ['Staging site', 'Daily backups', 'Assisted updates']],
            ['windows-vps', 'vps', 'Windows VPS', 'A virtual Windows server for compatible workloads.', 59.99, ['Windows image', 'Administrator access', 'Snapshot option']],
            ['cloud-configurable', 'cloud', 'Configurable Cloud Instance', 'Adjustable compute, memory, and storage for changing workloads.', 49.99, ['Flexible vCPU', 'Adjustable memory', 'Usage monitoring']],
            ['dedicated-server', 'dedicated', 'Dedicated Server', 'Single-tenant physical server for demanding applications.', 299.99, ['Dedicated resources', 'Remote management', 'Network monitoring']],
            ['gpu-compute', 'gpu', 'GPU Compute', 'GPU-backed compute for compatible AI and rendering workloads.', 499.99, ['GPU resources by availability', 'Isolated workload', 'Usage-based planning']],
            ['lab-environment', 'cloud', 'Lab & Test Environment', 'Temporary isolated environments for development and testing.', 24.99, ['Disposable environments', 'Snapshots', 'Usage limits']],
            ['managed-server', 'managed', 'Managed Server Administration', 'Server updates, monitoring, and routine administration.', 89.99, ['Patch coordination', 'Health checks', 'Incident escalation']],
            ['business-mailboxes', 'email', 'Business Mailboxes', 'Domain mailboxes for teams and organizations.', 14.99, ['Custom-domain addresses', 'Webmail and IMAP', 'Spam filtering']],
            ['email-forwarding', 'email', 'Email Forwarding & Aliases', 'Forwarding rules and aliases for domain email.', 4.99, ['Aliases', 'Forwarding rules', 'Domain integration']],
            ['smtp-relay', 'email', 'SMTP Relay', 'Application email delivery through an authenticated relay.', 9.99, ['SMTP credentials', 'Delivery logs', 'Sending limits']],
            ['calendar-contacts', 'email', 'Calendar & Contacts', 'Shared calendars and contacts for business mail users.', 12.99, ['Shared calendars', 'Contact sync', 'Account access controls']],
            ['email-archive', 'email', 'Email Archive & Backup', 'Retention and backup options for business email.', 19.99, ['Retention policies', 'Mailbox recovery', 'Export on request']],
            ['object-storage', 'storage', 'S3-Compatible Object Storage', 'Object storage for backups, media, and application data.', 12.99, ['S3-compatible API', 'Access keys', 'Usage reporting']],
            ['archive-storage', 'storage', 'Archive & File Storage', 'Lower-cost storage for files and long-term archives.', 8.99, ['Archive tiers', 'Access controls', 'Data retrieval']],
            ['vps-backup', 'backup', 'VPS Backup', 'Scheduled backup copies for virtual servers.', 14.99, ['Scheduled copies', 'Retention controls', 'Restore requests']],
            ['database-backup', 'backup', 'Database Backup', 'Managed backup copies for supported databases.', 9.99, ['Scheduled dumps', 'Retention controls', 'Restore assistance']],
            ['disaster-recovery', 'backup', 'Disaster Recovery', 'Recovery planning and protected copies for critical services.', 49.99, ['Recovery plan', 'Protected copies', 'Recovery coordination']],
            ['mysql-mariadb', 'database', 'MySQL & MariaDB Hosting', 'Managed relational databases for web applications.', 24.99, ['Managed instances', 'Private networking', 'Backup options']],
            ['postgresql-hosting', 'database', 'PostgreSQL Hosting', 'Managed PostgreSQL databases for production projects.', 29.99, ['Managed updates', 'Private access', 'Backup options']],
            ['redis-cache', 'database', 'Redis & Cache', 'Managed in-memory cache for supported applications.', 14.99, ['Private endpoint', 'Memory limits', 'Monitoring']],
            ['managed-databases', 'database', 'Managed Databases', 'Database operations and maintenance for supported engines.', 69.99, ['Maintenance windows', 'Access controls', 'Health monitoring']],
            ['dns-hosting', 'security', 'DNS Hosting', 'Authoritative DNS management for domains and services.', 4.99, ['DNS record management', 'Change history', 'Availability checks']],
            ['secondary-dns', 'security', 'Secondary DNS', 'Secondary DNS service for supported primary zones.', 3.99, ['Zone transfer support', 'Independent nameserver', 'Status checks']],
            ['ddos-protection', 'security', 'DDoS Protection', 'DDoS mitigation provided through specialist network partners.', 29.99, ['Provider-dependent mitigation', 'Traffic review', 'Incident coordination']],
            ['web-application-firewall', 'security', 'Web Application Firewall', 'Web traffic filtering for supported websites and applications.', 19.99, ['Managed rules', 'Traffic filtering', 'Event review']],
            ['vpn-business', 'security', 'Business VPN', 'Private remote access for business users and systems.', 12.99, ['User access controls', 'Encrypted tunnels', 'Connection monitoring']],
            ['uptime-monitoring', 'security', 'Uptime Monitoring & Alerts', 'Availability checks with notifications for monitored endpoints.', 6.99, ['Endpoint checks', 'Email alerts', 'Status history']],
            ['hosted-git', 'devops', 'Hosted Git Repositories', 'Private Git repositories for development teams.', 7.99, ['Private repositories', 'Team access', 'Audit history']],
            ['cicd-runners', 'devops', 'CI/CD Runners', 'Build runners for automated application delivery.', 14.99, ['Managed runners', 'Build minutes', 'Access controls']],
            ['staging-environments', 'devops', 'Staging Environments', 'Isolated preview environments before production release.', 11.99, ['Preview URLs', 'Access protection', 'Deployment snapshots']],
            ['docker-hosting', 'devops', 'Docker Container Hosting', 'Managed container runtime for applications and services.', 34.99, ['Container deployment', 'Resource limits', 'Log access']],
            ['private-registry', 'devops', 'Private Container Registry', 'Private image storage for container delivery workflows.', 8.99, ['Private images', 'Access tokens', 'Retention controls']],
            ['api-app-hosting', 'apps', 'API & Application Hosting', 'Managed application hosting for APIs and web services.', 29.99, ['Runtime monitoring', 'Deployment hooks', 'TLS endpoint']],
        ];

        foreach ($additionalProducts as $index => [$slug, $category, $name, $summary, $price, $features]) {
            Product::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'category' => $category, 'summary' => $summary, 'price' => $price,
                'billing_period' => 'monthly', 'features' => $features, 'is_active' => true,
                'is_featured' => false, 'sort_order' => $index + 10,
            ]);
        }

    }
}
