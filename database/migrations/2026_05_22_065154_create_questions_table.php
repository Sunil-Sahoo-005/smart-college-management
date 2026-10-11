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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('question_bank_id')
                ->constrained('question_banks')
                ->cascadeOnDelete();

            $table->enum('type', [
                'mcq',
                'true_false',
                'short_answer',
                'long_answer'
            ]);

            $table->longText('question');

            $table->decimal('mark', 5, 2)
                ->default(1);

            $table->decimal('negative_marks', 5, 2)
                ->default(0);

            $table->text('explanation')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
