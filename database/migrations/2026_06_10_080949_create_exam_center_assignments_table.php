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
        Schema::create('exam_center_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('center_id')
                ->constrained('exam_centers')
                ->cascadeOnDelete();

            $table->timestamps();

            /*
             * Prevent duplicate assignments
             */
            $table->unique([
                'exam_id',
                'center_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_center_assignments');
    }
};
