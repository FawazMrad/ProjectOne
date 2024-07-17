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
        Schema::create('fill_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->references('id')->on('stations');
            $table->foreignId('wallet_id')->references('id')->on('wallets');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fill_histories');
    }
};
