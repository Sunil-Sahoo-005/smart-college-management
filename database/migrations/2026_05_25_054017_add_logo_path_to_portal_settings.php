<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('portal_settings', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('logo_url');
            }
            if (!Schema::hasColumn('portal_settings', 'logoo_path')) {
                $table->string('logoo_path')->nullable()->after('logoo_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('portal_settings', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'logoo_path']);
        });
    }
};