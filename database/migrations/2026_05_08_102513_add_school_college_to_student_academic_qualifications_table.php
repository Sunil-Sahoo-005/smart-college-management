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
    Schema::table('student_academic_qualifications', function (Blueprint $table) {
        // Agar institution_name hai toh rename karo
        $table->renameColumn('institution_name', 'school_college');
        // Certificate path already hai, agar nahi hai toh add karo
        if (!Schema::hasColumn('student_academic_qualifications', 'certificate_path')) {
            $table->string('certificate_path')->nullable()->after('roll_number');
        }
    });
}

public function down()
{
    Schema::table('student_academic_qualifications', function (Blueprint $table) {
        $table->renameColumn('school_college', 'institution_name');
    });
}

};
