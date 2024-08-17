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
            $table->string('password', 255);
            $table->text('address', 255)->nullable();
            $table->string('phone_number', 20)->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->integer('points')->default(0);
            $table->float('rating')->default(0);
            $table->integer('followers')->default(0);
            $table->integer('following')->default(0);
            $table->longText('profile_pic')->nullable();
            $table->text('qr_code', 255)->nullable();
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
