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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');           // e.g., "B.Tech", "Class 1–10"
            $table->string('code');           // e.g., "B.Tech", "Class 1–10"
            $table->string('course_type')->default('semester');           // e.g., "B.Tech", "Class 1–10"
            $table->integer('duration_years'); // 4, 3, 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
