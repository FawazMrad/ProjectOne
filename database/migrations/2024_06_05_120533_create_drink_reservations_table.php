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
        Schema::create('drink_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drink_id')->references('id')->on('drinks');
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('total_price');
            $table->dateTime('serving_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drink_reservations');
    }
};
