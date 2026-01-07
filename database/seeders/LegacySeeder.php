<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LegacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Users
        DB::table('user')->insert([
            [
                'user_id' => 3,
                'user_firstname' => 'Cedrick Joseph',
                'user_lastname' => 'Mariano',
                'user_email' => 'marianocedrick3@gmail.com',
                'user_birthday' => '2004-08-03',
                'user_password' => Hash::make('password'), // Reset to 'password'
                'user_role' => 'admin',
                'user_phone' => '09602518414',
                'user_address' => 'Amaia Skies Shaw, Samat St., Brgy. Highway Hills, Mandaluyong City',
                'user_createdat' => '2025-11-04 09:08:07',
                'user_updatedat' => '2025-11-04 21:26:43',
            ],
            [
                'user_id' => 4,
                'user_firstname' => 'Borgy',
                'user_lastname' => 'Palermo',
                'user_email' => 'palermoborgy@gmail.com',
                'user_birthday' => null,
                'user_password' => Hash::make('password'),
                'user_role' => 'barista',
                'user_phone' => null,
                'user_address' => null,
                'user_createdat' => '2025-11-04 11:05:42',
                'user_updatedat' => '2025-11-06 14:02:20',
            ],
            [
                'user_id' => 5,
                'user_firstname' => 'Rj Jack',
                'user_lastname' => 'Florida',
                'user_email' => 'floridarj@barista.com',
                'user_birthday' => null,
                'user_password' => Hash::make('password'),
                'user_role' => 'barista',
                'user_phone' => null,
                'user_address' => null,
                'user_createdat' => '2025-11-06 14:03:04',
                'user_updatedat' => '2025-11-06 14:03:04',
            ],
        ]);

        // 2. Products
        DB::table('product')->insert([
            [
                'product_id' => 8,
                'product_name' => "Philosopher's Reserve",
                'product_description' => 'Single Origin (Exclusive Lot)',
                'product_price' => 850.00,
                'product_image' => 'assets/uploads/coffee-beans/philosopher-s-reserve-single-origin.webp',
                'product_status' => 'available',
                'product_category' => 'Coffee Beans', // Corrected from dump typo 'Pastry' based on path
                'product_featured' => 0,
                'product_createdat' => '2025-11-07 01:44:41',
                'product_updatedat' => '2025-11-07 09:26:06',
            ],
            [
                'product_id' => 9,
                'product_name' => 'The Parthenon Blend',
                'product_description' => 'House Blend (Balanced Arabica & Robusta)',
                'product_price' => 550.00,
                'product_image' => 'assets/uploads/coffee-beans/the-parthenon-blend-house-blend.webp',
                'product_status' => 'available',
                'product_category' => 'Coffee Beans',
                'product_featured' => 0,
                'product_createdat' => '2025-11-07 01:46:49',
                'product_updatedat' => '2025-11-07 09:26:39',
            ],
            // Adding extra products to ensure "Coffee of the Day" has variety and matches homepage assets
            [
                'product_id' => 10,
                'product_name' => "The Strategist's Latte",
                'product_description' => 'A smooth, creamy latte.',
                'product_price' => 150.00,
                'product_image' => 'assets/uploads/hot-brew/the-strategist-latte.webp',
                'product_status' => 'available',
                'product_category' => 'Hot Brew',
                'product_featured' => 1,
                'product_createdat' => now(),
                'product_updatedat' => now(),
            ],
            [
                'product_id' => 11,
                'product_name' => "The Oracle's Mocha",
                'product_description' => 'A rich and decadent mocha.',
                'product_price' => 160.00,
                'product_image' => 'assets/uploads/iced-&-cold/the-oracle-mocha.webp',
                'product_status' => 'available',
                'product_category' => 'Iced & Cold',
                'product_featured' => 1,
                'product_createdat' => now(),
                'product_updatedat' => now(),
            ],
            [
                'product_id' => 12,
                'product_name' => "Ambrosial Baklava",
                'product_description' => 'A heavenly pastry.',
                'product_price' => 120.00,
                'product_image' => 'assets/uploads/pastry/baklava.webp',
                'product_status' => 'available',
                'product_category' => 'Pastry',
                'product_featured' => 1,
                'product_createdat' => now(),
                'product_updatedat' => now(),
            ],
        ]);

        // 3. Orders
        DB::table('orders')->insert([
            [
                'order_id' => 7,
                'user_id' => 3,
                'order_status' => 'completed',
                'order_type' => 'pickup',
                'order_total' => 499.99,
                'order_payment_method' => 'card',
                'order_payment_status' => 'paid',
                'order_notes' => 'Customer requested extra sauce.',
                'order_delivery_address' => null,
                'order_createdat' => '2025-11-01 10:30:00',
                'order_updatedat' => '2025-11-01 11:00:00',
                'order_completedat' => '2025-11-01 11:00:00',
                'product_id' => 8,
            ],
            [
                'order_id' => 8,
                'user_id' => 4,
                'order_status' => 'ready',
                'order_type' => 'delivery',
                'order_total' => 899.50,
                'order_payment_method' => 'gcash',
                'order_payment_status' => 'paid',
                'order_notes' => 'Deliver before noon.',
                'order_delivery_address' => '123 Maple Street, Springfield',
                'order_createdat' => '2025-11-02 09:00:00',
                'order_updatedat' => '2025-11-02 09:45:00',
                'order_completedat' => null,
                'product_id' => 9,
            ],
        ]);
    }
}
