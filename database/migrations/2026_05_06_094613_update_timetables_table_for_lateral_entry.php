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
            $table->enum('entry_type', ['regular', 'lateral', 'both'])->default('regular')->after('section_id');
        });

        Schema::create('timetable_academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_academic_years');
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropColumn('entry_type');
        });
    }
};
