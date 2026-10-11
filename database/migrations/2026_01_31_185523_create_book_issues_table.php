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
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('issue_batch_id')->nullable()->index();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
             $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
             $table->foreignId('library_card_id')->nullable()->constrained('library_cards')->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->enum('status', ['issued', 'returned', 'overdue'])->default('issued');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
