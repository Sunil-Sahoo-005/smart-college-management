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
        Schema::table('timetables', function (Blueprint $table) {
            // Drop term_id
            // $table->dropForeign(['term_id']);
            // $table->dropColumn('term_id');

            // Add course_id
            $table->foreignId('course_id')->after('tenant_id')->nullable()->constrained('courses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');

            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
        });
    }
};
