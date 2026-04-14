<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository,
    ) {}

    public function getPaginatedOrders(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($filters, $perPage);
    }

    public function getOrderById(int $orderId): Order
    {
        return $this->orderRepository->findByIdOrFail($orderId);
    }

    public function loadOrder(Order $order): Order
    {
        return $this->orderRepository->loadRelations($order);
    }

    public function createOrder(
        int $userId,
        array $items,
        string $shippingAddress,
        string $shippingPhone,
        string $paymentMethod = Order::PAYMENT_METHOD_CASH,
    ): Order
    {
        return DB::transaction(function () use ($userId, $items, $shippingAddress, $shippingPhone, $paymentMethod): Order {
            if (! in_array($paymentMethod, Order::paymentMethods(), true)) {
                throw ValidationException::withMessages([
                    'payment_method' => ['Phương thức thanh toán không hợp lệ.'],
                ]);
            }

            $normalizedItems = [];
            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                if (! isset($normalizedItems[$productId])) {
                    $normalizedItems[$productId] = [
                        'product_id' => $productId,
                        'quantity' => 0,
                    ];
                }

                $normalizedItems[$productId]['quantity'] += $quantity;
            }

            $products = $this->productRepository->getByIdsForUpdate(array_keys($normalizedItems));

            if ($products->count() !== count($normalizedItems)) {
                throw ValidationException::withMessages([
                    'items' => ['Một hoặc nhiều sản phẩm không tồn tại.'],
                ]);
            }

            $total = 0;

            foreach ($normalizedItems as $normalizedItem) {
                $product = $products->get($normalizedItem['product_id']);

                if ($product->stock < $normalizedItem['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => [
                            sprintf('Sản phẩm ID %d không đủ tồn kho.', $product->id),
                        ],
                    ]);
                }

                $total += ((float) $product->price) * $normalizedItem['quantity'];
            }

            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'total_price' => $total,
                'status' => Order::STATUS_PENDING,
                'shipping_phone' => trim($shippingPhone),
                'shipping_address' => trim($shippingAddress),
                'payment_method' => $paymentMethod,
            ]);

            foreach ($normalizedItems as $normalizedItem) {
                $product = $products->get($normalizedItem['product_id']);

                $this->orderRepository->createDetail([
                    'order_id' => $order->id,
                    'product_id' => $normalizedItem['product_id'],
                    'quantity' => $normalizedItem['quantity'],
                    'price' => $product->price,
                ]);

                $this->productRepository->decrementStock($product, $normalizedItem['quantity']);
            }

            return $this->orderRepository->loadRelations($order);
        });
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        return $this->orderRepository->update($order, ['status' => $status]);
    }

    public function deleteOrder(Order $order): void
    {
        $this->orderRepository->delete($order);
    }
}
