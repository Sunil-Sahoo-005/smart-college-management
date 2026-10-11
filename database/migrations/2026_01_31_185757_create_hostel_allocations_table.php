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
        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('hostel_rooms')->cascadeOnDelete();
            $table->foreignId('bed_id')->constrained('hostel_beds')->cascadeOnDelete();
            $table->date('from_date');
            $table->date('to_date')->nullable();

            $table->unique(['student_id', 'from_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_allocations');
    }
};
