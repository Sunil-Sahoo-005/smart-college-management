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
        Schema::create('attendance_config_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_config_id')
                ->constrained('attendance_configs')
                ->cascadeOnDelete();

            // 0=Sunday, 1=Monday ... 6=Saturday
            $table->tinyInteger('day_of_week')->unsigned();

            $table->boolean('is_working_day')->default(true);

            $table->time('office_start_time')->nullable();   // null if non-working
            $table->time('office_end_time')->nullable();

            $table->integer('full_day_minutes')->nullable();  // null = derive from start/end
            $table->integer('half_day_minutes')->nullable();

            $table->unique(['attendance_config_id', 'day_of_week']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_config_days');
    }
};
