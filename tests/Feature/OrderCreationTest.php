<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_calculates_totals_and_reduces_stock(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 18,
            'stock_on_hand' => 5,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.subtotal', '200.00')
            ->assertJsonPath('data.tax', '36.00')
            ->assertJsonPath('data.grand_total', '236.00');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 3,
        ]);

        Queue::assertPushed(SendOrderConfirmationJob::class);
    }

    public function test_insufficient_stock_fails_without_creating_order_or_changing_stock(): void
    {
        $product = Product::factory()->create([
            'stock_on_hand' => 1,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Test',
                'email' => 'stock@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 1,
        ]);
    }

    public function test_customer_history_and_low_stock_endpoints_work(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'history@example.com',
        ]);

        $product = Product::factory()->create([
            'stock_on_hand' => 2,
        ]);

        $this->postJson('/api/orders', [
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ])->assertCreated();

        $this->getJson('/api/customers/history@example.com/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/products/low-stock')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $product->id,
            ]);
    }

    public function test_concurrent_orders_cannot_oversell_stock(): void
    {
        $product = Product::factory()->create([
            'price' => 100,
            'tax_percentage' => 18,
            'stock_on_hand' => 1,
        ]);

        $service = app(\App\Services\OrderService::class);

        $service->create([
            'customer' => [
                'name' => 'Customer One',
                'email' => 'customer1@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->expectException(ValidationException::class);

        $service->create([
            'customer' => [
                'name' => 'Customer Two',
                'email' => 'customer2@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);
    }
}