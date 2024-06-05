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
        Schema::create('ratings', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('events');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->text('comment')->nullable();
            $table->float('venue_rating', 8, 2)->nullable();
            $table->float('decor_rating', 8, 2)->nullable();
            $table->float('music_rating', 8, 2)->nullable();
            $table->float('food_rating', 8, 2)->nullable();
            $table->integer('drink_rating')->nullable();
            $table->float('aggregate_rating', 8, 2)->nullable();
            $table->primary(['event_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
