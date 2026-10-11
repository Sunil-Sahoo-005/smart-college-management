<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Basic Info
            $table->string('first_name');
            $table->string('last_name');

            // Contact
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();

            // Personal
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();

            // Address
            $table->text('permanent_address')->nullable();
            $table->text('address')->nullable();

            // Professional
            $table->string('employee_id', 50)->nullable();
            $table->string('bput_id', 50)->nullable();
            $table->string('employment_type', 50)->nullable();
            $table->foreignId('designation_id')->constrained('designations')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->date('joining_date');
            $table->date('leaving_date')->nullable();
            $table->date('resignation_date')->nullable();
            $table->enum('status', ['active', 'resigned'])->default('active');
            $table->tinyInteger('experience')->unsigned()->nullable();
            $table->string('qualification', 50)->nullable();
            $table->string('category', 20)->nullable();

            // Identity
            $table->string('pan', 10)->nullable();
            $table->string('aadhaar', 12)->nullable();
            $table->string('pf_number', 50)->nullable();

            // Bank
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_type', 20)->nullable();

            // Photo
            $table->string('photo_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};