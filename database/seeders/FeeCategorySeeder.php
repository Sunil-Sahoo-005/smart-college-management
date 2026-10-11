<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeeCategory;
use App\Models\Tenant;

class FeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $categories = [
            'Tuition',
            'Exam',
            'Lab',
            'Transport',
            'Hostel',
        ];

        // Get all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($categories as $category) {
                FeeCategory::create([
                    'tenant_id' => $tenant->id,
                    'name'      => $category,
                    'description' => $category . ' Fee',
                ]);
            }
        }
    }
}
