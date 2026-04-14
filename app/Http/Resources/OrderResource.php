<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'shipping_phone' => $this->shipping_phone,
            'shipping_address' => $this->shipping_address,
            'payment_method' => $this->payment_method,
            'payment_method_label' => Order::labelForPaymentMethod($this->payment_method),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'order_details_count' => $this->whenCounted('orderDetails'),
            'user' => $this->whenLoaded('user', function (): array {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'order_details' => OrderDetailResource::collection($this->whenLoaded('orderDetails')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
