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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admission_number')->unique();
            $table->string('roll_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('aadhaar', 12)->nullable()->unique();
            $table->string('email')->nullable()->after('aadhaar');//add
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('status', ['active', 'transfer_out', 'alumni','discontinued'])->default('active');
            $table->date('discontinue_date')->nullable();
            $table->text('discontinue_reason')->nullable();
            // ✅ Bank Details
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->enum('account_type', ['savings', 'current'])->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code', 11)->nullable();
            $table->timestamps();
            $table->string('category', 10)->nullable(); // ✅ Category
            $table->text('transfer_out_reason')->nullable();//new
            $table->enum('physically_handicapped', ['yes', 'no'])->default('no'); // add this
            $table->text('handicap_details')->nullable();                          // add this
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
