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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->enum('governorate', [
                'DAMASCUS', 'ALEPPO', 'IDLIB', 'HAMAH', 'LATTAKIA',
                'TARTOUS', 'HOMS', 'SWAIDA', 'DARAA', 'QUANYTIRA',
                'DAYRALZWR', 'ALHASAKAH', 'ALRAQQAH', 'RIFDIMASHQ'
            ]);
            $table->string('name', 255)->unique();
            $table->string('password');
            $table->string('location', 255);
            $table->string('manager_name', 255);
            $table->string('manager_email', 255)->unique();
            $table->text('manager_id_picture');
            $table->integer('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
