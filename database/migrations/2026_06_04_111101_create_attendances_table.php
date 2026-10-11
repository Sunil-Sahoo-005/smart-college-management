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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('working_minutes')->default(0);
            $table->boolean('is_late')->default(false);

            $table->enum('status', [
                'present',
                'absent',
                'half_day',
                'leave',
                'holiday',
                'week_off'
            ])->default('absent');

            $table->enum('source', [
                'manual',
                'biometric',
                'app'
            ])->default('manual');

            $table->text('remark')->nullable();

            $table->timestamps();

            $table->unique([
                'staff_id',
                'attendance_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
