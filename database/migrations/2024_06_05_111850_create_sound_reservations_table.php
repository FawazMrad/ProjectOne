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
        Schema::create('sound_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreignId('sound_id')->references('id')->on('sounds');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sound_reservations');
    }
};
