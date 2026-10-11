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
        Schema::create('student_scholarships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('scholarship_id')
                ->constrained('scholarships')
                ->cascadeOnDelete();

            $table->decimal('approved_amount', 12, 2);

            $table->decimal('used_amount', 12, 2)
                ->default(0);

            $table->decimal('balance_amount', 12, 2)
                ->default(0);

            $table->date('applied_on');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed'
            ])->default('approved');

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_scholarships');
    }
};
