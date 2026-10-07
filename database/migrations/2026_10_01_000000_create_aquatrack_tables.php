<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('full_name', 100);
            $t->string('username', 50)->unique();
            $t->string('password');
            $t->enum('role', ['owner', 'counter', 'delivery']);
            $t->enum('status', ['Active', 'Inactive'])->default('Active');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('customer_name', 100);
            $t->string('contact_number', 15)->nullable();
            $t->string('address')->nullable();
            $t->enum('customer_type', ['Household', 'Sari-sari Store', 'Office', 'Other'])->default('Household');
            $t->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $t) {
            $t->id();
            $t->string('item_name', 100);
            $t->enum('item_type', ['Container', 'Accessory', 'Raw Material']);
            $t->unsignedInteger('filled_qty')->default(0);
            $t->unsignedInteger('empty_qty')->default(0);
            $t->unsignedInteger('low_stock_level')->default(5);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->nullable()->constrained('inventory_items');
            $t->string('product_name', 100);
            $t->enum('category', ['Refill', 'New Container', 'Dispenser', 'Accessory']);
            $t->decimal('unit_price', 8, 2);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained('inventory_items');
            $t->foreignId('user_id')->constrained('users');
            $t->enum('movement_type', ['Refill', 'Sale', 'Return', 'Adjustment']);
            $t->integer('filled_change')->default(0);
            $t->integer('empty_change')->default(0);
            $t->string('remarks')->nullable();
            $t->timestamps();
        });

        Schema::create('sales', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_id')->nullable()->constrained('customers');
            $t->foreignId('user_id')->constrained('users');
            $t->enum('sale_type', ['Walk-in', 'Delivery']);
            $t->decimal('total_amount', 10, 2)->default(0);
            $t->enum('payment_status', ['Paid', 'Unpaid'])->default('Paid');
            $t->timestamps();
            $t->index('created_at');
        });

        Schema::create('sale_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $t->foreignId('product_id')->constrained('products');
            $t->unsignedInteger('quantity');
            $t->decimal('unit_price', 8, 2);   // copied so later price changes don't alter past sales
            $t->decimal('subtotal', 10, 2);
        });

        Schema::create('delivery_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_id')->constrained('customers');
            $t->foreignId('product_id')->constrained('products');
            $t->foreignId('assigned_to')->nullable()->constrained('users');
            $t->foreignId('sale_id')->nullable()->constrained('sales');
            $t->unsignedInteger('quantity');
            $t->string('delivery_address');
            $t->date('preferred_date');
            $t->time('preferred_time')->nullable();
            $t->enum('status', ['Pending', 'Assigned', 'Out for Delivery', 'Delivered', 'Cancelled'])->default('Pending');
            $t->timestamp('completed_at')->nullable();
            $t->string('remarks')->nullable();
            $t->timestamps();
            $t->index('status');
        });
    }

    public function down(): void
    {
        foreach (['delivery_requests', 'sale_items', 'sales', 'stock_movements', 'products', 'inventory_items', 'customers', 'users'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
