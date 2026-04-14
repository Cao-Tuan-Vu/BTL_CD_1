@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/orders/show.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Đơn hàng</h1>
            <p class="subtitle">Thông tin đơn hàng và các dòng sản phẩm.</p>
        </div>
        <div class="actions">
            <a class="btn muted" href="{{ route('admin.orders.index') }}">Quay lại</a>
            <a class="btn primary" href="{{ route('admin.orders.edit', $order) }}">Cập nhật trạng thái</a>
        </div>
    </div>

    <section class="grid two a-shared-section-gap">
        <article class="card">
            <p><strong>Người đặt:</strong> {{ $order->user?->name ?? 'Không có' }}</p>
            <p><strong>Email:</strong> {{ $order->user?->email ?? 'Không có' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->shipping_phone ?: 'Không có' }}</p>
            <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address ?: 'Không có' }}</p>
            <p><strong>Thanh toán:</strong> {{ \App\Models\Order::labelForPaymentMethod($order->payment_method) }}</p>
            <p><strong>Trạng thái:</strong> {{ \App\Models\Order::labelForStatus($order->status) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $order->created_at?->format('Y-m-d H:i') }}</p>
        </article>
        <article class="card">
            <p><strong>Tổng tiền:</strong> {{ number_format((float) $order->total_price, 2) }}</p>
            <p><strong>Số lượng:</strong> {{ (int) ($order->order_details_count ?? 0) }}</p>
        </article>
    </section>

    <section class="card">
        <h2 class="title a-shared-title-gap">Chi tiết đơn hàng</h2>
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tạm tính</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($order->orderDetails as $detail)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Sản phẩm">{{ $detail->product?->name ?? 'Không có' }}</td>
                    <td data-label="Số lượng">{{ $detail->quantity }}</td>
                    <td data-label="Giá">{{ number_format((float) $detail->price, 2) }}</td>
                    <td data-label="Tạm tính">{{ number_format((float) $detail->price * (int) $detail->quantity, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không có sản phẩm nào trong đơn hàng này.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection


