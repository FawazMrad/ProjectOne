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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->boolean('is_verified')->default(false);
            $table->string('password', 255);
            $table->string('address', 255)->nullable();
            $table->string('phone-number', 20)->nullable();
            $table->integer('age')->nullable();
            $table->integer('points')->default(0);
            $table->float('rating')->default(0);
            $table->string('profile_pic', 255)->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
