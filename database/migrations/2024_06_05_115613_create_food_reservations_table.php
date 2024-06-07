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
        Schema::create('food_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_id')->references('id')->on('food');
            $table->foreignId('event_id')->references('id')->on('events');
            $table->integer('quantity');
            $table->integer('total_price');
            $table->date('serving_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_reservations');
    }
};
