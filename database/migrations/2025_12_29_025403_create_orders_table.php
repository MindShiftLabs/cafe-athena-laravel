<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('user_id')->constrained('user', 'user_id')->onDelete('cascade');
            $table->enum('order_status', ['pending', 'preparing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('order_type', ['pickup', 'delivery'])->default('pickup');
            $table->decimal('order_total', 10, 2);
            $table->enum('order_payment_method', ['cash', 'card', 'gcash', 'paymaya'])->default('cash');
            $table->enum('order_payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->text('order_notes')->nullable();
            $table->text('order_delivery_address')->nullable();
            $table->dateTime('order_createdat')->useCurrent();
            $table->dateTime('order_updatedat')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('order_completedat')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('product', 'product_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
