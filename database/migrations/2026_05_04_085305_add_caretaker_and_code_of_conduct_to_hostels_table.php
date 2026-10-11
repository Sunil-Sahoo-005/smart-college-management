<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            if (!Schema::hasColumn('hostels', 'caretaker')) {
                $table->string('caretaker')->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('hostels', 'caretaker_phone')) {
                $table->string('caretaker_phone')->nullable()->after('caretaker');
            }
            if (!Schema::hasColumn('hostels', 'code_of_conduct')) {
                $table->text('code_of_conduct')->nullable()->after('caretaker_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn(['caretaker', 'caretaker_phone', 'code_of_conduct']);
        });
    }
};