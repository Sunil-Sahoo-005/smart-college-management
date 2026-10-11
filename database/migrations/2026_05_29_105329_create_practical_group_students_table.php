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
    Schema::create('practical_group_students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('practical_group_id')->constrained('practical_groups')->onDelete('cascade');
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->unique(['practical_group_id', 'student_id']);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('practical_group_students');
}
};
