<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Sample Product 1',
            'price' => '19.99',
            'description' => 'A sample product description.',
        ]);

        Product::create([
            'name' => 'Sample Product 2',
            'price' => '29.99',
            'description' => 'second product description.',
        ]);

        Product::create([
            'name' => 'Sample Product 3',
            'price' => '1.99',
            'description' => 'third product description.',
        ]);

        Product::create([
            'name' => 'Sample Product 4',
            'price' => '9.99',
            'description' => 'Another sample product description.',
        ]);
    }
}
