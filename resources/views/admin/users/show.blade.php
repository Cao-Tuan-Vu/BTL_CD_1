@extends('admin.layouts.app')

@section('title', 'Chi Tiết Người Dùng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/users/show.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Người dùng #{{ $user->id }}</h1>
            <p class="subtitle">Thông tin tài khoản và hoạt động gần đây.</p>
        </div>
        <div class="actions">
            <a class="btn muted" href="{{ route('admin.users.index') }}">Quay lại</a>
            <a class="btn primary" href="{{ route('admin.users.edit', $user) }}">Chỉnh sửa</a>
        </div>
    </div>

    <section class="card a-shared-section-gap">
        <p><strong>Họ tên:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p>
            <strong>Vai trò:</strong>
            @if ($user->role === 'admin')
                <span class="badge info">Quản trị</span>
            @else
                <span class="badge ok">Khách hàng</span>
            @endif
        </p>
        <p><strong>Tổng đơn hàng:</strong> {{ $user->orders_count ?? 0 }}</p>
        <p><strong>Tổng đánh giá:</strong> {{ $user->reviews_count ?? 0 }}</p>
        <p><strong>Xác minh email:</strong> {{ $user->email_verified_at?->format('Y-m-d H:i') ?? 'Chưa' }}</p>
        <p><strong>Ngày tạo:</strong> {{ $user->created_at?->format('Y-m-d H:i') }}</p>
    </section>

    <section class="card a-shared-section-gap">
        <h2 class="title a-shared-title-gap">Đơn hàng gần đây</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($recentOrders as $order)
                <tr>
                    <td data-label="ID">#{{ $order->id }}</td>
                    <td data-label="Tổng tiền">{{ number_format((float) $order->total_price, 2) }}</td>
                    <td data-label="Trạng thái">{{ \App\Models\Order::labelForStatus($order->status) }}</td>
                    <td data-label="Ngày tạo">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <a class="btn muted" href="{{ route('admin.orders.show', $order) }}">Xem</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không tìm thấy đơn hàng của người dùng này.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2 class="title a-shared-title-gap">Đánh giá gần đây</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Số sao</th>
                <th>Nội dung</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($recentReviews as $review)
                <tr>
                    <td data-label="ID">#{{ $review->id }}</td>
                    <td data-label="Sản phẩm">{{ $review->product?->name ?? 'Không có' }}</td>
                    <td data-label="Số sao">{{ $review->rating }}/5</td>
                    <td data-label="Nội dung">{{ $review->comment ?: 'Không có bình luận' }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <a class="btn muted" href="{{ route('admin.reviews.show', $review) }}">Xem</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không tìm thấy đánh giá của người dùng này.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection


