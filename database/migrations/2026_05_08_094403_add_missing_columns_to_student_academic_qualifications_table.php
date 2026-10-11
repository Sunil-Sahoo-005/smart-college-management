<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('student_academic_qualifications', function (Blueprint $table) {

            // Already existing ke baad add kar rahe hain
            $table->string('stream', 100)->nullable()->after('grade');
            $table->string('roll_number', 100)->nullable()->after('stream');

            $table->unsignedBigInteger('uploaded_by')->nullable()->after('roll_number');
            $table->string('certificate_path')->nullable()->after('uploaded_by');

            // Foreign key for uploaded_by
            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_academic_qualifications', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn([
                'stream',
                'roll_number',
                'uploaded_by',
                'certificate_path',
            ]);
        });
    }
};
