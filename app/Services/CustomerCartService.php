<?php

namespace App\Services;

use App\Models\Product;

class CustomerCartService
{
    protected const SESSION_KEY = 'customer_cart';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function items(): array
    {
        $rawCart = $this->rawCart();

        if (empty($rawCart)) {
            return [];
        }

        $products = Product::query()
            ->whereIn('id', array_keys($rawCart))
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($rawCart as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (! $product) {
                continue;
            }

            $cleanQuantity = (int) max(1, min((int) $quantity, max(1, (int) $product->stock)));
            $price = (float) $product->price;

            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'stock' => (int) $product->stock,
                'quantity' => $cleanQuantity,
                'subtotal' => $price * $cleanQuantity,
                'product' => $product,
            ];
        }

        return $items;
    }

    public function addItem(Product $product, int $quantity): void
    {
        $cart = $this->rawCart();
        $current = (int) ($cart[$product->id] ?? 0);

        $cart[$product->id] = (int) max(1, min($current + $quantity, max(1, (int) $product->stock)));

        $this->storeRawCart($cart);
    }

    public function updateItem(Product $product, int $quantity): void
    {
        $cart = $this->rawCart();

        if (! array_key_exists($product->id, $cart)) {
            return;
        }

        $cart[$product->id] = (int) max(1, min($quantity, max(1, (int) $product->stock)));

        $this->storeRawCart($cart);
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->rawCart();
        unset($cart[$productId]);

        $this->storeRawCart($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function itemCount(): int
    {
        return (int) array_sum($this->rawCart());
    }

    public function totalAmount(): float
    {
        $total = 0;

        foreach ($this->items() as $item) {
            $total += (float) $item['subtotal'];
        }

        return $total;
    }

    /**
     * @return array<int, array{product_id: int, quantity: int}>
     */
    public function toOrderItems(): array
    {
        $orderItems = [];

        foreach ($this->items() as $item) {
            $orderItems[] = [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ];
        }

        return $orderItems;
    }

    /**
     * @return array<int, int>
     */
    protected function rawCart(): array
    {
        /** @var array<int, int> $cart */
        $cart = session()->get(self::SESSION_KEY, []);

        return $cart;
    }

    /**
     * @param  array<int, int>  $cart
     */
    protected function storeRawCart(array $cart): void
    {
        session()->put(self::SESSION_KEY, $cart);
    }
}
