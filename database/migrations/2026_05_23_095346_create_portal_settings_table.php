<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate
     */
    public function up(): void
    {
        Schema::create('portal_settings', function (Blueprint $table) {
            $table->id();                                          // bigint UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('tenant_id');              // bigint UNSIGNED
            $table->string('portal_name')->nullable();            // varchar(255)
            $table->string('logo_url')->nullable();               // varchar(255)  — primary logo
            $table->string('logoo_url')->nullable();              // varchar(255)  — secondary logo
            $table->tinyInteger('parent_portal_enabled')
                  ->default(1);                                   // tinyint(1)
            $table->tinyInteger('student_portal_enabled')
                  ->default(1);                                   // tinyint(1)
            $table->timestamps();                                 // created_at, updated_at

            // Foreign key (tenant table zaroor honi chahiye)
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_settings');
    }
};