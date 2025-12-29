<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create USER Table
        Schema::create('user', function (Blueprint $table) {
            $table->id('user_id'); // Primary Key is user_id
            $table->string('user_firstname', 100);
            $table->string('user_lastname', 100);
            $table->string('user_email', 255)->unique();
            $table->date('user_birthday')->nullable();
            $table->string('user_password', 255);
            $table->enum('user_role', ['admin', 'barista', 'customer'])->default('customer');
            $table->string('user_phone', 20)->nullable();
            $table->text('user_address')->nullable();
            $table->timestamp('user_createdat')->useCurrent();
            $table->timestamp('user_updatedat')->useCurrent()->useCurrentOnUpdate();
        });

        // 2. Create PRODUCTS Table (Must be before order_items)
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name', 200);
            $table->text('product_description')->nullable();
            $table->decimal('product_price', 10, 2);
            $table->string('product_image', 255)->nullable(); // Changed from varchar to string
            $table->enum('product_status', ['available', 'unavailable'])->default('available');
            $table->string('product_category', 100); // Changed from varchar to string  
            $table->tinyInteger('product_stock')->default(1);
            $table->timestamp('product_createdat')->useCurrent();
            $table->timestamp('product_updatedat')->useCurrent()->useCurrentOnUpdate();
        });

        // 3. Create ORDERS Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            // FIX: Point to 'user' table and specify 'user_id' as the reference column
            $table->foreignId('user_id')->constrained('user', 'user_id')->onDelete('cascade');
            $table->enum('order_status', ['pending', 'ready', 'completed', 'canceled'])->default('pending');
            $table->enum('order_type', ['delivery', 'pickup'])->default('pickup');
            $table->decimal('order_total', 10, 2);
            $table->enum('order_payment_method', ['cash', 'card'])->default('cash');
            $table->enum('order_payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->text('order_delivery_address')->nullable();
            $table->timestamp('order_createdat')->useCurrent();
            $table->timestamp('order_updatedat')->useCurrent()->useCurrentOnUpdate();
            $table->time('order_pickup_time')->nullable();
        });

        // 4. Create ORDER_ITEMS Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            // FIX: Explicitly reference the custom primary keys
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->integer('orderitem_quantity');
            $table->decimal('orderitem_price', 10, 2);
            $table->decimal('orderitem_subtotal', 10, 2);
        });
    }

    public function down(): void
    {
        // Drop in reverse order to respect foreign keys
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('user');
    }
};