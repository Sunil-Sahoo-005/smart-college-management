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
        Schema::table('fee_structures', function (Blueprint $table) {
            if (! Schema::hasColumn('fee_structures', 'course_id')) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained('courses')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('fee_structures', 'year_number')) {
                $table->unsignedInteger('year_number')
                    ->nullable()
                    ->after('stream_id');
            }

            if (Schema::hasColumn('fee_structures', 'class_id')) {
                $table->foreignId('class_id')
                    ->nullable()
                    ->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            if (Schema::hasColumn('fee_structures', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }

            if (Schema::hasColumn('fee_structures', 'year_number')) {
                $table->dropColumn('year_number');
            }
        });
    }
};
