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
        // Schema::create('fee_structures', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
        //     $table->string('name'); // e.g., "Class 1 Annual"
        //     $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
        //     $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        //     $table->timestamps();
        // });
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('stream_id')->constrained('streams')->cascadeOnDelete(); // branch
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();   // semester
            $table->string('name'); // e.g., "CSE – Sem 1 Fee"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
