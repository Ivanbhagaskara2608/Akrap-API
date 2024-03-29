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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('scheduleId');
            $table->string('activity_name');
            $table->date('date');
            $table->string('location', 16);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('attendance_code');
            $table->enum('status', ["available", "unavailable"])->default("available");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
