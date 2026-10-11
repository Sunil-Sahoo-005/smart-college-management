<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_class_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('student_class_enrollments', 'course_id')) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->after('academic_year_id')
                    ->constrained('courses')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('student_class_enrollments', 'class_stream_id')) {
                $table->foreignId('class_stream_id')
                    ->nullable()
                    ->after('class_id')
                    ->constrained('class_stream_mappings')
                    ->nullOnDelete();
            }
        });

        DB::table('student_class_enrollments')
            ->leftJoin('sections', 'student_class_enrollments.section_id', '=', 'sections.id')
            ->leftJoin('class_stream_mappings', function ($join) {
                $join->on('class_stream_mappings.id', '=', 'sections.class_stream_id')
                    ->orOn('class_stream_mappings.class_id', '=', 'student_class_enrollments.class_id');
            })
            ->leftJoin('course_stream', 'course_stream.id', '=', 'class_stream_mappings.course_stream_id')
            ->where(function ($query) {
                $query->whereNull('student_class_enrollments.class_stream_id')
                    ->orWhereNull('student_class_enrollments.course_id');
            })
            ->update([
                'student_class_enrollments.class_stream_id' => DB::raw('COALESCE(student_class_enrollments.class_stream_id, sections.class_stream_id, class_stream_mappings.id)'),
                'student_class_enrollments.course_id' => DB::raw('COALESCE(student_class_enrollments.course_id, course_stream.course_id)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('student_class_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('student_class_enrollments', 'class_stream_id')) {
                $table->dropConstrainedForeignId('class_stream_id');
            }

            if (Schema::hasColumn('student_class_enrollments', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
        });
    }
};
