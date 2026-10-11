<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('vehicle_id')->nullable();
            $table->text('description')->nullable();
            $table->string('start_point')->nullable();
            $table->decimal('start_lat', 10, 7)->nullable(); // ✅ add
            $table->decimal('start_lng', 10, 7)->nullable(); // ✅ add
            $table->string('end_point')->nullable();
            $table->decimal('end_lat', 10, 7)->nullable();   // ✅ add
            $table->decimal('end_lng', 10, 7)->nullable();   // ✅ add
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};