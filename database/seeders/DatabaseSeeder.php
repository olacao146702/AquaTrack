<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts only. Change these passwords before any real use.
        User::create(['full_name' => 'Station Owner',  'username' => 'owner',   'password' => 'password', 'role' => 'owner']);
        User::create(['full_name' => 'Counter Staff',  'username' => 'counter', 'password' => 'password', 'role' => 'counter']);
        User::create(['full_name' => 'Delivery Rider', 'username' => 'rider',   'password' => 'password', 'role' => 'delivery']);

        $round = InventoryItem::create(['item_name' => 'Round container (20L)', 'item_type' => 'Container', 'filled_qty' => 40, 'empty_qty' => 25, 'low_stock_level' => 10]);
        $slim  = InventoryItem::create(['item_name' => 'Slim container (20L)',  'item_type' => 'Container', 'filled_qty' => 30, 'empty_qty' => 20, 'low_stock_level' => 10]);
        $disp  = InventoryItem::create(['item_name' => 'Water dispenser',       'item_type' => 'Accessory', 'filled_qty' => 6,  'empty_qty' => 0,  'low_stock_level' => 2]);

        // Prices and thresholds are placeholders; confirm real values with AquaPure.
        Product::create(['item_id' => $round->id, 'product_name' => 'Refill - round',   'category' => 'Refill',        'unit_price' => 25]);
        Product::create(['item_id' => $slim->id,  'product_name' => 'Refill - slim',    'category' => 'Refill',        'unit_price' => 25]);
        Product::create(['item_id' => $round->id, 'product_name' => 'New filled round', 'category' => 'New Container', 'unit_price' => 250]);
        Product::create(['item_id' => $slim->id,  'product_name' => 'New filled slim',  'category' => 'New Container', 'unit_price' => 250]);
        Product::create(['item_id' => $disp->id,  'product_name' => 'Dispenser',        'category' => 'Dispenser',     'unit_price' => 1200]);

        Customer::create(['customer_name' => 'Sample Household', 'contact_number' => '09170000000', 'address' => 'Purok 1, Sample St.', 'customer_type' => 'Household']);
    }
}
