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
        Schema::create('security_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')->references('id')->on('securities');
            $table->foreignId('event_id')->references('id')->on('events');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('guards_number');
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_reservations');
    }
};