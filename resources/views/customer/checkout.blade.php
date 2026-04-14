@extends('customer.layouts.app')

@section('title', 'Thanh Toán')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/checkout.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Thanh toán</h1>
            <p class="c-shared-subtitle">Xác nhận thông tin và đặt hàng tu giỏ hàng của ban.</p>
        </div>
        <a class="btn muted" href="{{ route('customer.cart.show') }}">Quay lại giỏ hàng</a>
    </div>

    <section class="grid two">
        <article class="card">
            <h2 class="c-shared-card-title">Thông tin khách hàng</h2>

            <p class="c-shared-meta-row"><strong>Họ tên:</strong> {{ $customer->name }}</p>
            <p class="c-shared-meta-row"><strong>Email:</strong> {{ $customer->email }}</p>

            <form method="post" action="{{ route('customer.checkout.store') }}" class="c-checkout-block">
                @csrf

                <div class="form-field">
                    <label for="shipping_phone">Số điện thoại nhận hàng</label>
                    <input id="shipping_phone" type="text" inputmode="numeric" pattern="[0-9]*" name="shipping_phone" value="{{ old('shipping_phone') }}" placeholder="Ví dụ: 0901234567" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>

                <div class="form-field">
                    <label for="shipping_address">Địa chỉ giao hàng</label>
                    <textarea id="shipping_address" name="shipping_address" rows="4" required>{{ old('shipping_address') }}</textarea>
                </div>

                <fieldset class="c-checkout-payment-fieldset">
                    <legend>Phương thức thanh toán</legend>

                    <div class="c-checkout-payment-options" role="radiogroup" aria-label="Phương thức thanh toán">
                        @foreach ($paymentMethods as $method)
                            <label class="c-checkout-payment-option" for="payment_method_{{ $method['code'] }}">
                                <input
                                    id="payment_method_{{ $method['code'] }}"
                                    type="radio"
                                    name="payment_method"
                                    value="{{ $method['code'] }}"
                                    data-payment-method
                                    data-method-code="{{ $method['code'] }}"
                                    @checked($selectedPaymentMethod === $method['code'])
                                    required
                                >
                                <span class="c-checkout-payment-option-content">
                                    <strong>{{ $method['label'] }}</strong>
                                    <small>{{ $method['description'] }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="c-checkout-payment-panels">
                    @foreach ($paymentMethods as $method)
                        <section
                            class="c-checkout-payment-panel"
                            data-payment-panel="{{ $method['code'] }}"
                            @if ($selectedPaymentMethod !== $method['code']) hidden @endif
                        >
                            @if ($method['has_qr'])
                                <p class="c-checkout-payment-hint">Quét QR để thanh toán bằng {{ $method['label'] }} trước khi đặt hàng.</p>
                                <img
                                    class="c-checkout-payment-qr"
                                    src="{{ $method['qr_url'] }}"
                                    alt="QR {{ $method['label'] }}"
                                    loading="lazy"
                                    width="220"
                                    height="220"
                                >
                                <p class="c-checkout-payment-subhint">Nếu không quét được QR, dùng nội dung chuyển khoản:</p>
                                <code class="c-checkout-payment-payload">{{ $method['qr_payload'] }}</code>
                            @else
                                <p class="c-checkout-payment-hint">Bạn sẽ thanh toán tiền mặt khi nhận hàng.</p>
                            @endif
                        </section>
                    @endforeach
                </div>

                <button class="btn primary" type="submit">Xác nhận đặt hàng</button>
            </form>
        </article>

        <article class="card">
            <h2 class="c-shared-card-title">Tổng quan đơn hàng</h2>

            <table>
                <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>SL</th>
                    <th>Tạm tính</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td data-label="Sản phẩm">{{ $item['name'] }}</td>
                        <td data-label="SL">{{ $item['quantity'] }}</td>
                        <td data-label="Tạm tính">{{ number_format((float) $item['subtotal'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <p class="c-checkout-block-2">Tổng thanh toán: {{ number_format((float) $totalAmount, 2) }}</p>
        </article>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/customer/checkout-payment.js') }}" defer></script>
@endpush


