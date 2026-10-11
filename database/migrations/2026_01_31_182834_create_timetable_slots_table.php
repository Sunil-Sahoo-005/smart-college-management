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
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
             $table->foreignId('timetable_id')->constrained('timetables')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Sun, 1=Mon, ..., 6=Sat
            $table->foreignId('period_id')->constrained('attendance_periods')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            $table->unique(['timetable_id', 'day_of_week', 'period_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
