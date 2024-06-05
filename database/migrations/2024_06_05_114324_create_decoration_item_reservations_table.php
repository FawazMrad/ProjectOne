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
        Schema::create('decoration_item_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_reservation_id')->references('id')->on('venue_reservations');
            $table->foreignId('decoration_item_id')->references('id')->on('decoration_items');
            $table->integer('quantity');
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decoration_item_reservations');
    }
};
