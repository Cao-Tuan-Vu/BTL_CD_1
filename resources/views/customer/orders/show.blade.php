@extends('customer.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/orders/show.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Đơn hàng</h1>
            <p class="c-shared-subtitle">Thông tin đơn hàng và danh sách sản phẩm đã mua.</p>
        </div>
        <a class="btn muted" href="{{ route('customer.orders.index') }}">Quay lại đơn hàng</a>
    </div>

    <section class="grid two c-shared-section-gap">
        <article class="card">
            <p class="c-shared-meta-row"><strong>Khách hàng:</strong> {{ $order->user?->name ?? 'Không có' }}</p>
            <p class="c-shared-meta-row"><strong>Email:</strong> {{ $order->user?->email ?? 'Không có' }}</p>
            <p class="c-shared-meta-row"><strong>Số điện thoại:</strong> {{ $order->shipping_phone ?: 'Không có' }}</p>
            <p class="c-shared-meta-row"><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address ?: 'Không có' }}</p>
            <p class="c-shared-meta-row"><strong>Thanh toán:</strong> {{ \App\Models\Order::labelForPaymentMethod($order->payment_method) }}</p>
            <p class="c-shared-meta-row"><strong>Trạng thái:</strong> {{ \App\Models\Order::labelForStatus($order->status) }}</p>
            <p class="c-shared-meta-row"><strong>Ngày tạo:</strong> {{ $order->created_at?->format('Y-m-d H:i') }}</p>
        </article>

        <article class="card">
            <p class="c-shared-meta-row"><strong>Tổng số lượng:</strong> {{ (int) ($order->order_details_count ?? 0) }}</p>
            <p class="c-shared-meta-row"><strong>Tổng thanh toán:</strong> {{ number_format((float) $order->total_price, 2) }}</p>
        </article>
    </section>

    <section class="card">
        <h2 class="c-shared-card-title">Sản phẩm đã mua</h2>

        <table>
            <thead>
            <tr>
                <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tạm tính</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($order->orderDetails as $detail)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Sản phẩm">{{ $detail->product?->name ?? 'Không có' }}</td>
                    <td data-label="Giá">{{ number_format((float) $detail->price, 2) }}</td>
                    <td data-label="Số lượng">{{ $detail->quantity }}</td>
                    <td data-label="Tạm tính">{{ number_format((float) $detail->price * (int) $detail->quantity, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không tìm thấy sản phẩm trong đơn hàng này.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection


