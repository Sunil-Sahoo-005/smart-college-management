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
         Schema::table('academic_years', function (Blueprint $table) {
            $table->integer('start_year')->after('name');
            $table->integer('end_year')->after('start_year');

            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->dropColumn(['start_year', 'end_year']);
        });
    }
};
