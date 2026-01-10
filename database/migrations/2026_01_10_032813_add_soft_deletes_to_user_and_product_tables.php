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
        Schema::table('user', function (Blueprint $table) {
            $table->timestamp('user_deletedat')->nullable()->after('user_updatedat');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->timestamp('product_deletedat')->nullable()->after('product_updatedat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('user_deletedat');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('product_deletedat');
        });
    }
};