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
        Schema::create('course_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('year_number');      // 1,2,3,4
            $table->integer('semester_number')->nullable(); // 1,2
            $table->string('name');              // "Year 1 – Semester 1"

            $table->timestamps();

            $table->unique(['course_id', 'year_number', 'semester_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_stages');
    }
};
