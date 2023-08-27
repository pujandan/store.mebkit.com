<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $id = request('id');
        $size = request('size', 10);
        $page = request('page', 1);
        $filter = request(['filter.search', 'filter.price_from', 'filter.price_to']);

        // find by id
        if ($id) {
            $product = Product::with('category', 'galleries')->find($id);
            if ($product) {
                return ResponseFormatter::success($product, "data product berhasil diambil");
            } else {
                return ResponseFormatter::error("produk tidak ditemukan", 404);
            }
        }

        $product = Product::with('category', 'galleries')
            ->filter($filter)
            ->paginate($size, ['*'], 'page', $page);

        return ResponseFormatter::success($product, trans('message.show_success'));
    }
}
