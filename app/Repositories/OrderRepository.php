<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Order::query()
            ->select($this->orderColumns())
            ->with(['user:id,name,email'])
            ->withCount('orderDetails');

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = (int) max(1, min(100, $perPage));

        return $query->latest('id')->paginate($perPage);
    }

    public function findByIdOrFail(int $id): Order
    {
        return Order::query()
            ->select($this->orderColumns())
            ->with($this->detailRelations())
            ->withCount('orderDetails')
            ->findOrFail($id);
    }

    public function create(array $attributes): Order
    {
        return Order::create($attributes);
    }

    public function createDetail(array $attributes): OrderDetail
    {
        return OrderDetail::create($attributes);
    }

    public function update(Order $order, array $attributes): Order
    {
        $order->update($attributes);

        return $order->refresh()
            ->load($this->detailRelations())
            ->loadCount('orderDetails');
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }

    public function loadRelations(Order $order): Order
    {
        return $order->load($this->detailRelations())->loadCount('orderDetails');
    }

    /**
     * @return array<int, string>
     */
    protected function orderColumns(): array
    {
        return [
            'id',
            'user_id',
            'total_price',
            'status',
            'shipping_phone',
            'shipping_address',
            'payment_method',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function detailRelations(): array
    {
        return [
            'user:id,name,email',
            'orderDetails' => function ($builder): void {
                $builder
                    ->select(['id', 'order_id', 'product_id', 'quantity', 'price'])
                    ->with('product:id,name');
            },
        ];
    }
}
