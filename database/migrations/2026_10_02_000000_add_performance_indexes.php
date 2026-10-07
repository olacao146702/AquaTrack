<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $t) {
            $t->index('customer_name');
        });
        Schema::table('sales', function (Blueprint $t) {
            $t->index('customer_id');
            $t->index(['sale_type', 'payment_status']);
        });
        Schema::table('sale_items', function (Blueprint $t) {
            $t->index('product_id');
        });
        Schema::table('stock_movements', function (Blueprint $t) {
            $t->index(['item_id', 'created_at']);
        });
        Schema::table('delivery_requests', function (Blueprint $t) {
            $t->index(['assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', fn (Blueprint $t) => $t->dropIndex(['customer_name']));
        Schema::table('sales', function (Blueprint $t) {
            $t->dropIndex(['customer_id']);
            $t->dropIndex(['sale_type', 'payment_status']);
        });
        Schema::table('sale_items', fn (Blueprint $t) => $t->dropIndex(['product_id']));
        Schema::table('stock_movements', fn (Blueprint $t) => $t->dropIndex(['item_id', 'created_at']));
        Schema::table('delivery_requests', fn (Blueprint $t) => $t->dropIndex(['assigned_to', 'status']));
    }
};
