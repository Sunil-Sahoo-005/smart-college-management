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
        Schema::create('exam_attempts', function (Blueprint $table) {
             $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('exam_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('center_id')
                ->constrained('exam_centers')
                ->cascadeOnDelete();

            $table->foreignId('device_id')
                ->nullable()
                ->constrained('center_devices')
                ->nullOnDelete();

            /*
             * Attempt Information
             */
            $table->timestamp('started_at')->nullable();

            $table->timestamp('submitted_at')->nullable();

            /*
             * Current Status
             */
            $table->enum('status', [
                'not_started',
                'in_progress',
                'submitted',
                'evaluated',
                'expired',
            ])->default('not_started');

            /*
             * Final Score
             */
            $table->decimal('score', 8, 2)
                ->default(0);

            /*
             * Security Information
             */
            $table->string('ip_address')
                ->nullable();

            $table->text('device_info')
                ->nullable();

            $table->timestamps();

            /*
             * Prevent multiple attempts
             */
            $table->unique([
                'exam_id',
                'student_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
