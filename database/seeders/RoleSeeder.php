<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'module' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'module' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Librarian',
                'guard_name' => 'web',
                'module' => 'students',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Academic',
                'guard_name' => 'web',
                'module' => 'Academic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accountant',
                'guard_name' => 'web',
                'module' => 'Accountant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transport',
                'guard_name' => 'web',
                'module' => 'Transport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hostel',
                'guard_name' => 'web',
                'module' => 'Hostel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hr',
                'guard_name' => 'web',
                'module' => 'HR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}