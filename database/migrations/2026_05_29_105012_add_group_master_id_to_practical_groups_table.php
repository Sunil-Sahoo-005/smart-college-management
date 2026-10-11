<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    if (!Schema::hasColumn('practical_groups', 'group_master_id')) {
        Schema::table('practical_groups', function (Blueprint $table) {
            $table->foreignId('group_master_id')->nullable()->constrained('group_masters')->after('stream_id');
        });
    }
}

public function down()
{
    Schema::table('practical_groups', function (Blueprint $table) {
        $table->dropForeign(['group_master_id']);
        $table->dropColumn('group_master_id');
    });
}
};
