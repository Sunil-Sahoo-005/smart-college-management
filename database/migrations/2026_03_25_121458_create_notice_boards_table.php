<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notice_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->date('notice_date');
            $table->string('file_path')->nullable(); // PDF upload
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_boards');
    }
};