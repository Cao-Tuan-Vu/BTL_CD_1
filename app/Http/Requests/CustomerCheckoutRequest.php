<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'shipping_phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9]+$/'],
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
            'payment_method' => ['required', 'string', Rule::in(Order::paymentMethods())],
        ];
    }
}
