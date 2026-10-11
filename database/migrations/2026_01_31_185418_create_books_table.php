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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable();
            $table->string('category')->nullable();
            $table->integer('total_copies');
            $table->string('copy_isbn')->nullable();
            $table->enum('status', ['available', 'damaged', 'lost', 'under_repair'])->default('available');
            $table->string('book_image')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('pages')->nullable();
            $table->string('price')->nullable();
            $table->string('volume')->nullable();
            $table->string('year')->nullable();

            $table->foreignId('room_id')->nullable()->constrained('library_masters')->nullOnDelete();
            $table->foreignId('self_id')->nullable()->constrained('library_masters')->nullOnDelete();
            $table->foreignId('row_id')->nullable()->constrained('library_masters')->nullOnDelete();

            $table->string('order_id')->nullable()->after('tenant_id'); // groups books from same entry
            $table->string('bill_no')->nullable()->after('order_id');
            $table->date('bill_date')->nullable()->after('bill_no');
            $table->string('withdrawal_no')->nullable()->after('bill_date');
            $table->date('withdrawal_date')->nullable()->after('withdrawal_no');
            $table->string('bill_pdf')->nullable()->after('withdrawal_date');
            $table->text('remarks')->nullable()->after('bill_pdf');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
