<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        // find by id
        if ($id) {
            $categories = ProductCategory::find($id);

            if ($categories) {
                return ResponseFormatter::success($categories, "data kategori berhasil diambil");
            } else {
                return ResponseFormatter::error(null, "data kategori tidak tersedia", 400);
            }
        }

        $categories = ProductCategory::query();

        if ($name) {
            $categories->where('name', 'like', '%' . $name . '%');
        }

        if ($show_product) {
            $categories->with('products');
        }

        return ResponseFormatter::success($categories->paginate($limit), "data kategori berhasil diambil");
    }
}
