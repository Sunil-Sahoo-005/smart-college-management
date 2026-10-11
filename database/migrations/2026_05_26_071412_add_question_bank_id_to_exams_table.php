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
    Schema::table('exams', function (Blueprint $table) {
        $table->unsignedBigInteger('question_bank_id')->nullable()->after('exam_mode');
    });
}

public function down()
{
    Schema::table('exams', function (Blueprint $table) {
        $table->dropColumn('question_bank_id');
    });
}
};
