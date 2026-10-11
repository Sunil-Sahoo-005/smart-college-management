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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
             $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_installment_id')->nullable()
              ->constrained('fee_installments')->cascadeOnDelete();
            $table->enum('payment_method', ['cash', 'online', 'bank_transfer']);
            $table->string('transaction_id')->nullable();
            $table->string('gateway_name')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->dateTime('paid_at')->nullable();
            $table->date('payment_date');
            $table->string('receipt_number')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
