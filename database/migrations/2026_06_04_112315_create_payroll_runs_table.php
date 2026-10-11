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
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('month');

            $table->unsignedSmallInteger('year');

            $table->enum('status', [
                'draft',
                'processed',
                'finalized',
                'paid'
            ])->default('draft');

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('processed_at')->nullable();

            $table->timestamp('finalized_at')->nullable();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'month',
                'year'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
