<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    public function index(Request $request)
    {
        try {

            $id = $request->input('id');
            $page = $request->input('page', 1);
            $size = $request->input('size', 10);
            $name = $request->input('filter.name');
            $show_product = $request->input('show.product');

            // find by id
            if ($id) {
                $categories = ($show_product) ? ProductCategory::with('products')->find($id) : ProductCategory::find($id);

                if ($categories) {
                    return ResponseFormatter::success($categories, trans('message.show_success'));
                } else {
                    return ResponseFormatter::error(trans('message.show_failed'));
                }
            }

            $categories = ($show_product) ? ProductCategory::with('products') : ProductCategory::query();

            // search name
            if ($name) {
                $categories->where('name', 'like', '%' . $name . '%');
            }

            // pagination
            $categories = $categories->paginate($size, ['*'], 'page', $page);

            return ResponseFormatter::success($categories, trans('message.show_success'));
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }
}
