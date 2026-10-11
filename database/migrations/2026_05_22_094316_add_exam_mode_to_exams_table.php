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
        $table->enum('exam_mode', ['online', 'offline'])->default('offline')->after('exam_type_id');
    });
}

public function down()
{
    Schema::table('exams', function (Blueprint $table) {
        $table->dropColumn('exam_mode');
    });
}
    
};
