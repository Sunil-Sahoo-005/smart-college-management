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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            // Student enrollment (student + class + section + stream + academic year)
            $table->foreignId('enrollment_id');

            $table->date('date');
            $table->foreignId('slot_id')->nullable()->constrained('timetable_slots')->nullOnDelete();

            $table->enum('status', ['present', 'absent', 'late', 'half_day']);

            // Optional remarks
            $table->text('remarks')->nullable();

            $table->foreignId('marked_by_user_id')->constrained('staff')->cascadeOnDelete();

            // Prevent duplicate attendance
            $table->unique(
                ['enrollment_id', 'date', 'slot_id'],
                'uniq_student_attendance'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};
