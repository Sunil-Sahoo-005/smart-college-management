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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('from_stream_name');           // "CSE"
            $table->string('from_class_name');            // "Sem 1"
            $table->string('from_section_name')->nullable();  // "A"

            $table->string('to_stream_name');             // "CSE"
            $table->string('to_class_name');              // "Sem 2"
            $table->string('to_section_name')->nullable();    // "A"

            $table->decimal('gpa_required', 3, 2)->nullable();
            $table->decimal('student_gpa', 3, 2)->nullable();
            $table->text('remarks')->nullable();

            $table->enum('status', ['promoted', 'repeated', 'failed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
