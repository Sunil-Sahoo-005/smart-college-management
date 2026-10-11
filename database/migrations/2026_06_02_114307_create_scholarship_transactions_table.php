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
        Schema::create('scholarship_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_scholarship_id')
                ->constrained('student_scholarships')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('installment_id')->nullable();

            $table->foreign('installment_id')
                ->references('id')
                ->on('fee_installments')
                ->nullOnDelete();

            $table->decimal('amount_used', 12, 2);

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('installment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_transactions');
    }
};
