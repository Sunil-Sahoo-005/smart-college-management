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
        Schema::create('merged_class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->date('schedule_date');
            $table->foreignId('period_id')->constrained('attendance_periods')->cascadeOnDelete();

            // Multiple stream/class/section combinations
            $table->json('class_combinations'); // [{"stream_id":1,"class_id":1,"section_id":1},{"stream_id":1,"class_id":1,"section_id":2}]
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['schedule_date', 'period_id']);
            $table->index(['staff_id', 'schedule_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merged_class_schedules');
    }
};
