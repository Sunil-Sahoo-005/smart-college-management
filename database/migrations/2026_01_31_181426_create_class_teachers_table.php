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
        Schema::create('class_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('stream_id');
            $table->string('class_id');
            $table->string('section_id');
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('academic_year_id');

            $table->unique(['class_id', 'section_id', 'academic_year_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_teachers');
    }
};
