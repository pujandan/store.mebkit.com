<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
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
                return ResponseFormatter::error(null, "data transaksi tidak tersedia", 400);
            }
        }

        $transaction = Transaction::with('details.product')->where('user_id', Auth::user()->id);

        if ($status) {
            $transaction->where('status',  $status);
        }

        return ResponseFormatter::success($transaction->paginate($limit), "data transaksi berhasil diambil");
    }
}
