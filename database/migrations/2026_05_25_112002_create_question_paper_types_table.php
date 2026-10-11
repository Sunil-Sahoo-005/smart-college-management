<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_paper_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('question_bank_id');
            $table->string('type_name');
            $table->integer('total_marks');
            $table->integer('duration');
            $table->integer('total_questions');
            $table->text('description')->nullable();
            $table->timestamps();

            // Optional: foreign key constraints (adjust table names as needed)
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('question_bank_id')->references('id')->on('question_banks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_paper_types');
    }
};