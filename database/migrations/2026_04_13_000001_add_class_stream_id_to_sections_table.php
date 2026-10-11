<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (! Schema::hasColumn('sections', 'class_stream_id')) {
                $table->foreignId('class_stream_id')
                    ->nullable()
                    ->after('class_id')
                    ->constrained('class_stream_mappings')
                    ->nullOnDelete();
            }
        });

        DB::table('sections')
            ->whereNull('class_stream_id')
            ->update([
                'class_stream_id' => DB::raw('(
                    SELECT csm.id
                    FROM class_stream_mappings AS csm
                    WHERE csm.class_id = sections.class_id
                    LIMIT 1
                )'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'class_stream_id')) {
                $table->dropConstrainedForeignId('class_stream_id');
            }
        });
    }
};
