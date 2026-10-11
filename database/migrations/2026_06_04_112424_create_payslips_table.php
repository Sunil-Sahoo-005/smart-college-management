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
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('payroll_run_id')
                ->constrained('payroll_runs')
                ->cascadeOnDelete();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->decimal('gross_salary', 12, 2);

            $table->decimal('total_earnings', 12, 2)
                ->default(0);

            $table->decimal('total_deductions', 12, 2)
                ->default(0);

            $table->decimal('lop_amount', 12, 2)
                ->default(0);

            $table->decimal('bonus_amount', 12, 2)
                ->default(0);

            $table->decimal('arrear_amount', 12, 2)
                ->default(0);

            $table->decimal('overtime_amount', 12, 2)
                ->default(0);

            $table->decimal('net_salary', 12, 2);

            // Attendance Snapshot
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('half_days')->default(0);
            $table->integer('late_marks')->default(0);

            // Salary Breakdown Snapshot
            $table->json('earnings_breakdown')->nullable();

            $table->json('deductions_breakdown')->nullable();

            $table->boolean('is_published')
                ->default(false);

            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'payroll_run_id',
                'staff_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
