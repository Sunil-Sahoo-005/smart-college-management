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
        Schema::create('teacher_attendance', function (Blueprint $table) {
            $table->id();
             $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('period_id')->nullable()->constrained('attendance_periods')->nullOnDelete();
            $table->enum('status', ['present', 'absent', 'late', 'half_day']);
            $table->text('remarks')->nullable();
            $table->foreignId('marked_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->unique(['staff_id', 'date', 'period_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance');
    }
};
