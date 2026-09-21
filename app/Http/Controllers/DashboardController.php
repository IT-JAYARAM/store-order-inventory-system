<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', [
            'productCount' => Product::count(),
            'customerCount' => Customer::count(),
            'orderCount' => Order::count(),
            'lowStockProducts' => Product::where('stock_on_hand', '<=', config('store.low_stock_threshold'))->orderBy('stock_on_hand')->get(),
            'recentOrders' => Order::with('customer')->latest()->limit(10)->get(),
        ]);
    }
}
