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
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('event_id')->references('id')->on('events');
            $table->enum('status', ['invited', 'attending', 'purchased', 'cancelled']);
            $table->boolean('checked_in')->default(false);
            $table->dateTime('purchase_date')->nullable();
            $table->decimal('ticket_price', 10, 2)->default(0);
            $table->enum('ticket_type', ['regular', 'VIP']);
            $table->string('seat_number', 20)->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('qr_code', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
