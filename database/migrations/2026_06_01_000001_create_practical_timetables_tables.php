<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practical_timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('practical_group_id')->constrained('practical_groups')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('practical_timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('practical_timetable_id')->constrained('practical_timetables')->cascadeOnDelete();
            $table->foreignId('attendance_period_id')->constrained('attendance_periods')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->timestamps();

            $table->unique(['practical_timetable_id', 'day_of_week', 'attendance_period_id'], 'uniq_practical_timetable_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practical_timetable_slots');
        Schema::dropIfExists('practical_timetables');
    }
};
