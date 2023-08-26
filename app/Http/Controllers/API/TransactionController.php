<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');

        // find by id
        if ($id) {
            $transaction = Transaction::with('details.product')->find($id);

            if ($transaction) {
                return ResponseFormatter::success($transaction, "data transaksi berhasil diambil");
            } else {
                return ResponseFormatter::error("transaksi tidak ditemukan", 404);
            }
        }

        $transaction = Transaction::with('details.product')->where('user_id', Auth::user()->id);

        if ($status) {
            $transaction->where('status',  $status);
        }

        return ResponseFormatter::success($transaction->paginate($limit), "data transaksi berhasil diambil");
    }


    public function checkout(Request $request)
    {
        try {

            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'exists:products,id',
                'price_total' => 'required',
                'price_shipping' => 'required',
                'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED',
            ]);

            $transaction = Transaction::create([
                'user_id' => Auth::user()->id,
                'address' => $request->address,
                'price_total' => $request->price_total,
                'price_shipping' => $request->price_shipping,
                'status' => $request->status,
            ]);

            foreach ($request->items as $product) {
                TransactionDetail::create([
                    'user_id' => Auth::user()->id,
                    'transaction_id' => $transaction->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            return ResponseFormatter::success($transaction->load('details.product'), "Transaksi Berhasil");
        } catch (\Exception $e) {
            return ResponseFormatter::exception($e);
        }
    }
}
