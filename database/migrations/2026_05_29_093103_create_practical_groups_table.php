<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('practical_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('stream_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
        });

        // Pivot table for many-to-many with academic years
        Schema::create('practical_group_academic_years', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('practical_group_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->timestamps();

            $table->foreign('practical_group_id')->references('id')->on('practical_groups')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('practical_group_academic_years');
        Schema::dropIfExists('practical_groups');
    }
};