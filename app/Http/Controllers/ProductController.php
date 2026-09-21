<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function lowStock(): JsonResponse
    {
        $threshold = (int) request()->integer('threshold', config('store.low_stock_threshold'));
        $threshold = max(0, $threshold);
        return response()->json([
            'threshold' => $threshold,
            'data' => Product::where('stock_on_hand', '<=', $threshold)->orderBy('stock_on_hand')->get(),
        ]);
    }
}
