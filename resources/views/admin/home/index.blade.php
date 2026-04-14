@extends('admin.layouts.app')

@section('title', 'Tổng Quan')
@section('meta_description', 'Trang tổng quan quản trị HomeSpace: thống kê nhanh sản phẩm, đơn hàng, doanh thu và đánh giá.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/home/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Tổng quan</h1>
            <p class="subtitle">Thống kê nhanh sản phẩm, danh mục, đơn hàng và đánh giá.</p>
        </div>
    </div>

    <section class="grid stats a-shared-section-gap">
        <article class="card">
            <div class="subtitle">Sản phẩm</div>
            <h2 class="title">{{ number_format($stats['products']) }}</h2>
        </article>
        <article class="card">
            <div class="subtitle">Danh mục</div>
            <h2 class="title">{{ number_format($stats['categories']) }}</h2>
        </article>
        <article class="card">
            <div class="subtitle">Đơn hàng</div>
            <h2 class="title">{{ number_format($stats['orders']) }}</h2>
        </article>
        <article class="card">
            <div class="subtitle">Đánh giá</div>
            <h2 class="title">{{ number_format($stats['reviews']) }}</h2>
        </article>
    </section>

    <section class="card a-shared-section-gap">
        <div class="subtitle">Doanh thu</div>
        <h2 class="title">{{ number_format($stats['revenue'], 2) }}</h2>
    </section>

    <section class="card">
        <div class="toolbar">
            <h2 class="title">Đơn hàng mới nhất</h2>
            <a class="btn muted" href="{{ route('admin.orders.index') }}">Xem tất cả đơn hàng</a>
        </div>

        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Người đặt</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
                <th>Ngày tạo</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($latestOrders as $order)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Người đặt">{{ $order->user?->name ?? 'Không có' }}</td>
                    <td data-label="Trạng thái">{{ \App\Models\Order::labelForStatus($order->status) }}</td>
                    <td data-label="Tổng tiền">{{ number_format((float) $order->total_price, 2) }}</td>
                    <td data-label="Ngày tạo">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <a class="btn muted" href="{{ route('admin.orders.show', $order) }}">Chi tiết</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Chưa có đơn hàng nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection


