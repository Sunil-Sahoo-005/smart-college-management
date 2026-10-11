<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::table('routes', function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable()->change();
    });

    Schema::table('vehicles', function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable()->change();
    });

    // route_stops mein tenant_id nahi hai — to ADD karo
    Schema::table('route_stops', function (Blueprint $table) {
        if (!Schema::hasColumn('route_stops', 'tenant_id')) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
        }
    });

    Schema::table('drivers', function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('routes', function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
    });

    Schema::table('vehicles', function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
    });

    Schema::table('route_stops', function (Blueprint $table) {
        if (Schema::hasColumn('route_stops', 'tenant_id')) {
            $table->dropColumn('tenant_id');
        }
    });

    Schema::table('drivers', function (Blueprint $table) {
        $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
    });
}
};
