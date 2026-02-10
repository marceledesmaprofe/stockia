<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Electronics',
            'description' => 'Electronic devices and accessories',
            'status' => true,
            'business_id' => 1
        ]);

        Category::create([
            'name' => 'Clothing',
            'description' => 'Apparel and fashion items',
            'status' => true,
            'business_id' => 1
        ]);

        Category::create([
            'name' => 'Home & Kitchen',
            'description' => 'Household items and kitchen appliances',
            'status' => true,
            'business_id' => 1
        ]);

        Category::create([
            'name' => 'Books',
            'description' => 'Books and educational materials',
            'status' => false, // Inactive category
            'business_id' => 1
        ]);
    }
}
