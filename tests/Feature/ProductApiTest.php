<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_with_filter_and_pagination(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $categoryA->id,
            'price' => 300,
            'status' => 'active',
        ]);

        Product::factory()->create([
            'category_id' => $categoryA->id,
            'price' => 50,
            'status' => 'active',
        ]);

        Product::factory()->create([
            'category_id' => $categoryB->id,
            'price' => 500,
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/products?category_id='.$categoryA->id.'&min_price=100&per_page=5');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('meta.per_page', 5);
    }

    public function test_it_creates_product_successfully(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $category = Category::factory()->create();

        $payload = [
            'name' => 'Sofa Nordic',
            'description' => 'Fabric sofa for living room',
            'price' => 1200,
            'status' => 'active',
            'stock' => 20,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Sofa Nordic');

        $this->assertDatabaseHas('products', [
            'name' => 'Sofa Nordic',
            'category_id' => $category->id,
        ]);
    }
}
