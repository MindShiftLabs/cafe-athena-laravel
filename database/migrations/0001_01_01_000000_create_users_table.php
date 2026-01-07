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
        // 1. Create USER Table
        Schema::create('user', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_firstname', 100);
            $table->string('user_lastname', 100);
            $table->string('user_email', 255)->unique();
            $table->date('user_birthday')->nullable();
            $table->string('user_password', 255);
            $table->enum('user_role', ['admin', 'barista', 'customer'])->default('customer');
            $table->string('user_phone', 20)->nullable();
            $table->text('user_address')->nullable();
            $table->dateTime('user_createdat')->useCurrent();
            $table->dateTime('user_updatedat')->useCurrent()->useCurrentOnUpdate();
        });

        // 2. Create Password Reset Tokens Table (Standard Laravel, good to have)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Create Sessions Table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Note: Pointing to 'user_id' on 'user' table
            $table->foreignId('user_id')->nullable()->index(); 
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user');
    }
};