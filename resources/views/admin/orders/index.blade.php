@extends('admin.layouts.app')

@section('title', 'Đơn Hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/orders/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Đơn hàng</h1>
            <p class="subtitle">Theo dõi và cập nhật trạng thái đơn hàng.</p>
        </div>
    </div>

    <section class="card a-shared-section-gap">
        <form method="get" action="{{ route('admin.orders.index') }}">
            <select name="user_id">
                <option value="">Tất cả người dùng</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <select name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                        {{ \App\Models\Order::labelForStatus($status) }}
                    </option>
                @endforeach
            </select>

            <button class="btn primary" type="submit">Loc</button>
            <a class="btn muted" href="{{ route('admin.orders.index') }}">Đặt lại</a>
        </form>
    </section>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Người đặt</th>
                <th>Trạng thái</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Ngày tạo</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Người đặt">{{ $order->user?->name ?? 'Không có' }}</td>
                    <td data-label="Trạng thái">{{ \App\Models\Order::labelForStatus($order->status) }}</td>
                    <td data-label="Số lượng">{{ (int) ($order->order_details_count ?? 0) }}</td>
                    <td data-label="Tổng tiền">{{ number_format((float) $order->total_price, 2) }}</td>
                    <td data-label="Ngày tạo">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <div class="actions a-shared-actions-end">
                            <a class="btn muted" href="{{ route('admin.orders.show', $order) }}">Xem</a>
                            <a class="btn muted" href="{{ route('admin.orders.edit', $order) }}">Cập nhật trạng thái</a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="post" onsubmit="return confirm('Xóa đơn hàng này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Không tìm thấy đơn hàng nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $orders->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection


