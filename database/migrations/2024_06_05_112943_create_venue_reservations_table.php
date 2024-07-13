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
        Schema::create('venue_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->references('id')->on('venues');
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('booked_seats')->nullable()->default(0);
            $table->integer('booked_vip_seats')->nullable()->default(0);
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_reservations');
    }
};
