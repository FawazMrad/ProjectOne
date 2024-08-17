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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location', 100);
            $table->string('location_on_map', 255)->nullable();
            $table->integer('max_capacity_no_chairs');
            $table->integer('max_capacity_chairs');
            $table->integer('vip_chairs')->nullable();
            $table->boolean('is_vip');
            $table->string('website', 255)->nullable();
            $table->float('rating')->default(0);
            $table->longText('image', 255)->nullable();
            $table->integer('cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
