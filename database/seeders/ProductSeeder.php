<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $products = collect([
        //     [
        //         "name" => "Nasi Padang Rendang",
        //         "price" => 15000,
        //         "description" => "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Repellat, temporibus ratione sit numquam itaque doloribus laboriosam quos, voluptas at asperiores vitae accusamus, voluptatum deserunt vero commodi laborum delectus! Harum ipsum, fugit voluptatibus perferendis repellat veritatis fugiat dolor culpa odio enim impedit aut dolores eos doloremque voluptates iste dolorum totam? Modi.",
        //         "tags" => "makanan,nasi",
        //         "category_id" => "99fb16a5-6f47-421c-8d47-63860ca94da7",
        //     ]
        // ]);


        // foreach ($products as $product) {
        //     Product::create($product);
        // }

        Product::create(
            [
                "name" => "Nasi Padang Rendang",
                "price" => 15000,
                "description" => "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Repellat, temporibus ratione sit numquam itaque doloribus laboriosam quos, voluptas at asperiores vitae accusamus, voluptatum deserunt vero commodi laborum delectus! Harum ipsum, fugit voluptatibus perferendis repellat veritatis fugiat dolor culpa odio enim impedit aut dolores eos doloremque voluptates iste dolorum totam? Modi.",
                "tags" => "makanan,nasi",
                "category_id" => ProductCategory::get()->first()->id,
            ]
        );
    }
}
