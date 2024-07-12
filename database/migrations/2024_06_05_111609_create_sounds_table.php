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
        Schema::create('sounds', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['LIVE', 'RECORDED', 'DJ']);
            $table->string('genre', 50);
            $table->string('artist', 50)->nullable();
            $table->integer('rating')->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sounds');
    }
};
