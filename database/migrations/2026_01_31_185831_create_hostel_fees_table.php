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
        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
             $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('hostel_fees');
    }
};
