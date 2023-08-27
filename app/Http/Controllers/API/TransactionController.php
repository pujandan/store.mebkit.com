<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function index()
    {
        try {
            $id = request('id');
            $page = request('page', 1);
            $size = request('size', 10);
            $filter = request(['filter.status']);

            // Show
            if ($id) {
                $transaction = Transaction::with('details.product')->find($id);
                if ($transaction) {
                    return ResponseFormatter::success($transaction, trans('message.show_success'));
                } else {
                    return ResponseFormatter::error(trans('message.empty'));
                }
            }

            // Get
            $transaction = Transaction::with('details.product')
                ->where('user_id', Auth::user()->id)
                ->filter($filter)
                ->paginate($size, ['*'], 'page', $page);

            return ResponseFormatter::success($transaction, trans('message.show_success'));
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }


    public function checkout(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'items' => 'required|array',
                // 'items.*.id' => 'exists:products,id',
                'price_total' => 'required',
                'price_shipping' => 'required',
                'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED',
            ]);

            $transaction = Transaction::create([
                'address' => $request->address,
                'price_total' => $request->price_total,
                'price_shipping' => $request->price_shipping,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ]);

            // check product available
            $productIds =  collect($request->items)->map(function ($e) {
                return $e['id'];
            });
            $products = Product::whereIn('id', $productIds)->get();

            foreach ($request->items as $product) {
                $inProduct = $products->where('id', $product['id'])->first();
                if ($inProduct) {
                    // insert to table details
                    TransactionDetail::create([
                        'user_id' => Auth::user()->id,
                        'transaction_id' => $transaction->id,
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                    ]);
                } else {
                    throw new \Exception(trans('message.product_empty', ['name' => $product['name']]));
                }
            }

            DB::commit();
            return ResponseFormatter::success($transaction->load('details.product'), trans('message.transaction_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::exception($e);
        }
    }
}
