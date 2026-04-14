<?php

namespace App\Services;

use App\Models\Order;

class PaymentMethodService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCheckoutMethods(float $orderAmount): array
    {
        $amount = (int) max(0, round($orderAmount));

        $momoPayload = sprintf(
            'MOMO|HOMESPACE|amount=%d|order=CHECKOUT|note=Thanh toan don hang',
            $amount,
        );

        $vnpayPayload = sprintf(
            'VNPAY|HOMESPACE|amount=%d|order=CHECKOUT|note=Thanh toan don hang',
            $amount,
        );

        return [
            [
                'code' => Order::PAYMENT_METHOD_CASH,
                'label' => Order::labelForPaymentMethod(Order::PAYMENT_METHOD_CASH),
                'description' => 'Thanh toán khi nhận hàng (COD).',
                'has_qr' => false,
                'qr_url' => null,
                'qr_payload' => null,
            ],
            [
                'code' => Order::PAYMENT_METHOD_MOMO,
                'label' => Order::labelForPaymentMethod(Order::PAYMENT_METHOD_MOMO),
                'description' => 'Quét mã QR MoMo để thanh toán trước khi xác nhận đơn.',
                'has_qr' => true,
                'qr_url' => $this->buildQrUrl($momoPayload),
                'qr_payload' => $momoPayload,
            ],
            [
                'code' => Order::PAYMENT_METHOD_VNPAY,
                'label' => Order::labelForPaymentMethod(Order::PAYMENT_METHOD_VNPAY),
                'description' => 'Quét mã QR VNPay để thanh toán trước khi xác nhận đơn.',
                'has_qr' => true,
                'qr_url' => $this->buildQrUrl($vnpayPayload),
                'qr_payload' => $vnpayPayload,
            ],
        ];
    }

    protected function buildQrUrl(string $payload): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode($payload);
    }
}
