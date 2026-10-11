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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');                      // e.g. Casual, Sick, Earned
            $table->integer('allowed_days');
            $table->boolean('carry_forward')->default(false);
            $table->boolean('is_paid')->default(true);
            $table->enum('applicable_to', ['all', 'teaching', 'non_teaching'])->default('all');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
