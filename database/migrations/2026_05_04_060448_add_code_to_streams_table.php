<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('streams', function (Blueprint $table) {
        if (!Schema::hasColumn('streams', 'code')) {
            $table->string('code', 20)->after('name')->nullable(false);
            $table->unique(['tenant_id', 'code']);
        }
    });
}

    public function down(): void
    {
        Schema::table('streams', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'code']);
            $table->dropColumn('code');
        });
    }
};