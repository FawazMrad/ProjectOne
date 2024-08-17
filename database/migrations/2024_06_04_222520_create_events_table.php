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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('category_id')->references('id')->on('categories');
            $table->string('title', 100);
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('min_age')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_private')->default(false);
            $table->enum('attendance_type', ['INVITATION', 'TICKET']);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('ticket_price', 10, 2)->default(0);
            $table->decimal('vip_ticket_price', 10, 2)->default(0);
            $table->longText('image', 255)->nullable();
            $table->text('qr_code')->nullable();
            $table->float('rating')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
