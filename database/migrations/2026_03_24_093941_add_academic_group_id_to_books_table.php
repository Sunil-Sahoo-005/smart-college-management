<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcademicGroupIdToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the column already exists
        if (!Schema::hasColumn('books', 'academic_group_id')) {
            Schema::table('books', function (Blueprint $table) {
                // Add academic_group_id column after row_id
                $table->unsignedBigInteger('academic_group_id')->nullable()->after('row_id');
                
                // Add foreign key constraint
                $table->foreign('academic_group_id')
                      ->references('id')
                      ->on('academic_groups')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            // Check if foreign key exists before dropping
            $foreignKeys = $this->getForeignKeys('books');
            if (in_array('books_academic_group_id_foreign', $foreignKeys)) {
                $table->dropForeign(['academic_group_id']);
            }
            
            // Drop the column if it exists
            if (Schema::hasColumn('books', 'academic_group_id')) {
                $table->dropColumn('academic_group_id');
            }
        });
    }
    
    /**
     * Get foreign keys for a table
     *
     * @param string $table
     * @return array
     */
    private function getForeignKeys($table)
    {
        $conn = Schema::getConnection();
        $databaseName = $conn->getDatabaseName();
        
        $results = $conn->select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$databaseName, $table]);
        
        return array_column($results, 'CONSTRAINT_NAME');
    }
}