<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('exam_questions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('exam_id');
        $table->unsignedBigInteger('question_id');
        $table->timestamps();

        $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
        $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        $table->unique(['exam_id', 'question_id']);
    });
}

public function down()
{
    Schema::dropIfExists('exam_questions');
}
};
