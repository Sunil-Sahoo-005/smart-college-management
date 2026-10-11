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
        // Schema::create('student_fee_structures', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
        //     $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        //     $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
        //     $table->date('start_date');
        //     $table->date('end_date');
        //     $table->unique(['student_id', 'fee_structure_id']);
        //     $table->timestamps();
        // });
        Schema::create('student_fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->unique(['student_id', 'fee_structure_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_structures');
    }
};
