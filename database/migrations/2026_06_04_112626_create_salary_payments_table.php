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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('payslip_id')
                ->constrained('payslips')
                ->cascadeOnDelete();

            $table->date('payment_date');

            $table->decimal('amount', 12, 2);

            $table->enum('payment_mode', [
                'bank_transfer',
                'cash',
                'cheque',
                'upi'
            ]);

            $table->string('transaction_reference')
                ->nullable();

            $table->enum('status', [
                'pending',
                'paid',
                'failed'
            ])->default('pending');

            $table->text('remarks')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
