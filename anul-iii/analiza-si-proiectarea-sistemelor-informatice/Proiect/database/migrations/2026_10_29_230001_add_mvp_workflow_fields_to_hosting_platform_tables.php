<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('cpu_cores')->nullable();
            $table->unsignedInteger('ram_mb')->nullable();
            $table->unsignedInteger('storage_gb')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('hostname')->nullable()->unique();
            $table->date('expires_at')->nullable();
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 40)->default('general');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->char('currency', 3)->default('RON');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->char('currency', 3)->default('RON');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn('category');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['hostname']);
            $table->dropColumn(['hostname', 'expires_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cpu_cores', 'ram_mb', 'storage_gb']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn('is_active');
        });
    }
};
