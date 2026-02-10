<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'High-performance laptop for work and gaming',
            'barcode' => '1234567890123',
            'current_stock' => 10,
            'sale_price' => 999.99,
            'status' => true,
            'business_id' => 1
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest model smartphone with advanced features',
            'barcode' => '9876543210987',
            'current_stock' => 25,
            'sale_price' => 699.99,
            'status' => true,
            'business_id' => 1
        ]);

        Product::create([
            'name' => 'Wireless Headphones',
            'description' => 'Noise-cancelling wireless headphones',
            'barcode' => '5678901234567',
            'current_stock' => 50,
            'sale_price' => 199.99,
            'status' => true,
            'business_id' => 1
        ]);

        Product::create([
            'name' => 'Tablet',
            'description' => 'Portable tablet for entertainment and productivity',
            'barcode' => '3456789012345',
            'current_stock' => 15,
            'sale_price' => 399.99,
            'status' => false, // Inactive product
            'business_id' => 1
        ]);
    }
}
