<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::with('customer')->find($this->orderId);
        if (!$order) return;

        Log::info('Simulated order confirmation email', [
            'order_id' => $order->id,
            'customer_email' => $order->customer->email,
            'grand_total' => $order->grand_total,
        ]);
    }
}
