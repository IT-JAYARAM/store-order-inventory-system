<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $customer = Customer::query()->updateOrCreate(
                ['email' => strtolower(trim($data['customer']['email']))],
                ['name' => trim($data['customer']['name'])]
            );

            $productIds = collect($data['items'])->pluck('product_id')->sort()->values();
            $products = Product::query()->whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0.0;
            $tax = 0.0;
            $prepared = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);
                $quantity = (int) $item['quantity'];

                if (!$product || $product->stock_on_hand < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => [sprintf('Insufficient stock for product %s. Available: %d.', $product?->name ?? $item['product_id'], $product?->stock_on_hand ?? 0)],
                    ]);
                }

                $lineSubtotal = round((float) $product->price * $quantity, 2);
                $lineTax = round($lineSubtotal * ((float) $product->tax_percentage / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $tax += $lineTax;
                $prepared[] = compact('product', 'quantity', 'lineSubtotal', 'lineTax', 'lineTotal');
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => round($subtotal, 2),
                'tax' => round($tax, 2),
                'grand_total' => round($subtotal + $tax, 2),
            ]);

            foreach ($prepared as $line) {
                /** @var Product $product */
                $product = $line['product'];
                $product->decrement('stock_on_hand', $line['quantity']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $line['quantity'],
                    'unit_price' => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'line_subtotal' => $line['lineSubtotal'],
                    'line_tax' => $line['lineTax'],
                    'line_total' => $line['lineTotal'],
                ]);
            }

            return $order->load(['customer', 'items.product']);
        });

        SendOrderConfirmationJob::dispatch($order->id);

        return $order;
    }
}
