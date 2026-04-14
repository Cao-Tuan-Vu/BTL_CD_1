<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_order_and_reduces_stock(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        $productA = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100,
            'stock' => 10,
        ]);

        $productB = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50,
            'stock' => 5,
        ]);

        $payload = [
            'shipping_phone' => '0901234567',
            'shipping_address' => '123 Đường Nguyễn Trãi, Quận 1, TP.HCM',
            'items' => [
                [
                    'product_id' => $productA->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $productB->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.total_price', '250.00');
        $response->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
            'shipping_phone' => '0901234567',
            'shipping_address' => '123 Đường Nguyễn Trãi, Quận 1, TP.HCM',
        ]);

        $this->assertDatabaseHas('order_details', [
            'product_id' => $productA->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('order_details', [
            'product_id' => $productB->id,
            'quantity' => 1,
        ]);

        $this->assertSame(8, $productA->refresh()->stock);
        $this->assertSame(4, $productB->refresh()->stock);
    }

    public function test_it_rejects_order_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100,
            'stock' => 1,
        ]);

        $payload = [
            'shipping_phone' => '0912345678',
            'shipping_address' => '456 Đường Lê Lợi, Quận 1, TP.HCM',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['items']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_cannot_update_order_status(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'total_price' => 100,
            'status' => 'pending',
            'shipping_phone' => '0923456789',
            'shipping_address' => '12 Nguyễn Huệ, Quận 1, TP.HCM',
        ]);

        $response = $this->actingAs($customer)->patchJson(
            "/api/orders/{$order->id}/status",
            ['status' => 'completed']
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_cannot_create_order(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 200,
            'stock' => 10,
        ]);

        $payload = [
            'shipping_phone' => '0900000000',
            'shipping_address' => '123 Lê Lợi, Quận 1, TP.HCM',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($admin)->postJson('/api/orders', $payload);

        $response->assertForbidden();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::query()->create([
            'user_id' => $customer->id,
            'total_price' => 100,
            'status' => 'pending',
            'shipping_phone' => '0934567890',
            'shipping_address' => '34 Lê Lợi, Quận 1, TP.HCM',
        ]);

        $response = $this->actingAs($admin)->patchJson(
            "/api/orders/{$order->id}/status",
            ['status' => 'completed']
        );

        $response->assertOk();
        $response->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }
}
