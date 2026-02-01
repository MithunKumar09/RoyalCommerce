<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_settings', function (Blueprint $table) {
            $table->id();

            // Future SuperAdmin readiness (multi-scope). For now, single "platform" row.
            $table->string('scope_type', 32)->default('platform'); // platform|merchant|vendor (future)
            $table->unsignedBigInteger('scope_id')->nullable();

            // enabled|disabled|whatsapp_only
            $table->string('mode', 32)->default('enabled');

            // Feature-level switches (capabilities)
            $table->boolean('cart_enabled')->default(true);
            $table->boolean('checkout_enabled')->default(true);
            $table->boolean('orders_enabled')->default(true); // order placement
            $table->boolean('wallet_enabled')->default(true);
            $table->boolean('order_tracking_enabled')->default(true);

            // WhatsApp-only mode (future)
            $table->string('whatsapp_number', 64)->nullable();
            $table->text('whatsapp_message_template')->nullable();

            // Audit (admin-controlled now; future superadmin)
            $table->unsignedBigInteger('updated_by_admin_id')->nullable();

            $table->timestamps();

            $table->index(['scope_type', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commerce_settings');
    }
};

