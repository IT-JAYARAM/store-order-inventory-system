<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function create(): View
    {
        return view('orders.create', ['products' => Product::orderBy('name')->get()]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->create($request->validated());
        return response()->json(['message' => 'Order created successfully.', 'data' => $order], 201);
    }

    public function storeWeb(StoreOrderRequest $request)
    {
        $order = $this->orders->create($request->validated());
        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
    }

    public function history(string $email): JsonResponse
    {
        $customer = \App\Models\Customer::where('email', strtolower(trim($email)))->first();
        if (!$customer) return response()->json(['message' => 'Customer not found.'], 404);

        $orders = $customer->orders()->with(['items.product'])->latest()->get();
        return response()->json(['data' => $orders]);
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items']);
        return view('orders.show', compact('order'));
    }
}
