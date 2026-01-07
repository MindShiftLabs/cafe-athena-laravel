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
        Schema::create('product', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name', 200);
            $table->text('product_description')->nullable();
            $table->decimal('product_price', 10, 2);
            $table->string('product_image', 255)->nullable();
            $table->enum('product_status', ['available', 'unavailable'])->default('available');
            $table->string('product_category', 100);
            $table->tinyInteger('product_featured')->default(0);
            $table->dateTime('product_createdat')->useCurrent();
            $table->dateTime('product_updatedat')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
