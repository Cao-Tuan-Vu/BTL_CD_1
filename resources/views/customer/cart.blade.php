@extends('customer.layouts.app')

@section('title', 'Giỏ Hàng')
@section('meta_description', 'Xem giỏ hàng HomeSpace, cập nhật số lượng sản phẩm và tiến hành thanh toán nhanh chóng.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/cart.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Giỏ hàng</h1>
            <p class="c-shared-subtitle">Xem lai sản phẩm da chọn trước khi thanh toán.</p>
        </div>
        <a class="btn muted" href="{{ route('customer.products.index') }}">Tiếp tục mua sắm</a>
    </div>

    @if (count($items) === 0)
        <section class="card">
            <p class="c-cart-block-2">Giỏ hàng của ban đang trống.</p>
            <a class="btn primary" href="{{ route('customer.products.index') }}">Xem sản phẩm</a>
        </section>
    @else
        <section class="grid two">
            <article class="card">
                <table>
                    <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tạm tính</th>
                        <th class="text-right">Tac vu</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td data-label="Sản phẩm">
                                <strong>{{ $item['name'] }}</strong>
                                <div class="c-cart-block-3">Tồn kho: {{ $item['stock'] }}</div>
                            </td>
                            <td data-label="Giá">{{ number_format((float) $item['price'], 2) }}</td>
                            <td data-label="Số lượng">
                                <form class="actions" method="post" action="{{ route('customer.cart.update', $item['product']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" min="1" max="{{ max(1, (int) $item['stock']) }}" value="{{ $item['quantity'] }}" class="c-cart-qty-input">
                                    <button class="btn muted" type="submit">Cập nhật</button>
                                </form>
                            </td>
                            <td data-label="Tạm tính">{{ number_format((float) $item['subtotal'], 2) }}</td>
                            <td data-label="Tac vu" class="text-right">
                                <form method="post" action="{{ route('customer.cart.remove', $item['product']) }}" onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </article>

            <article class="card c-cart-block">
                <h2 class="c-shared-card-title">Tổng quan đơn hàng</h2>

                <p class="c-shared-meta-row"><strong>Số lượng:</strong> {{ count($items) }}</p>
                <p class="c-shared-meta-row"><strong>Tổng tiền:</strong> {{ number_format((float) $totalAmount, 2) }}</p>

                <div class="actions c-shared-actions-top">
                    <a class="btn primary block" href="{{ route('customer.checkout.show') }}">Tiến hành thanh toán</a>
                    <a class="btn muted block" href="{{ route('customer.products.index') }}">Thêm sản phẩm</a>
                </div>
            </article>
        </section>
    @endif
@endsection


