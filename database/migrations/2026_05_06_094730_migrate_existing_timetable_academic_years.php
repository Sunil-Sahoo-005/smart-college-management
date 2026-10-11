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
        $timetables = DB::table('timetables')->whereNotNull('academic_year_id')->get();
        foreach ($timetables as $timetable) {
            DB::table('timetable_academic_years')->insert([
                'timetable_id' => $timetable->id,
                'academic_year_id' => $timetable->academic_year_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('timetable_academic_years')->truncate();
    }
};
