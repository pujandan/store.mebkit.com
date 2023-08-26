<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = collect([
            ["name" => "Makanan"],
            ["name" => "Minuman"],
            ["name" => "Buah"],
            ["name" => "Cemilan"],
        ]);

        foreach ($categories as $value) {
            ProductCategory::create($value);
        }
    }
}
