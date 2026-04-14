@extends('customer.layouts.app')

@section('title', 'Theo Dõi Đơn Hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/orders/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Theo dõi đơn hàng</h1>
            <p class="c-shared-subtitle">Xem và lọc đơn hàng của tài khoản theo từng trạng thái.</p>
        </div>
        <a class="btn muted" href="{{ route('customer.products.index') }}">Mua thêm sản phẩm</a>
    </div>

    <section class="card c-shared-section-gap">
        <div class="toolbar c-orders-index-block">
            <div class="chips">
                <a class="chip {{ $selectedStatus === '' ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Tất cả</a>
                @foreach ($statuses as $status)
                    <a class="chip {{ $selectedStatus === $status ? 'active' : '' }}" href="{{ route('customer.orders.index', ['status' => $status]) }}">
                        {{ \App\Models\Order::labelForStatus($status) }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Khách hàng</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
                <th>Số lượng</th>
                <th>Ngày tạo</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Khách hàng">{{ $order->user?->name ?? 'Không có' }}</td>
                    <td data-label="Trạng thái">
                        <span class="badge {{ \App\Models\Order::badgeClassForStatus($order->status) }}">
                            {{ \App\Models\Order::labelForStatus($order->status) }}
                        </span>
                    </td>
                    <td data-label="Tổng tiền">{{ number_format((float) $order->total_price, 2) }}</td>
                    <td data-label="Số lượng">{{ (int) ($order->order_details_count ?? 0) }}</td>
                    <td data-label="Ngày tạo">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                    <td data-label="Tac vu" class="text-right">
                        <a class="btn muted" href="{{ route('customer.orders.show', $order) }}">Chi tiết</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Không có đơn hàng nào phù hợp.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="c-orders-index-block-3">
            {{ $orders->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection



