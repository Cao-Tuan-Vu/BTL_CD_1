@extends('admin.layouts.app')

@section('title', 'Cập Nhật Trạng Thái Đơn Hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/orders/edit.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Cập nhật đơn hàng</h1>
            <p class="subtitle">Chi thay doi trạng thái đơn hàng.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.orders.show', $order) }}">Quay lại</a>
    </div>

    <section class="card">
        <p><strong>Người đặt:</strong> {{ $order->user?->name ?? 'Không có' }}</p>
        <p><strong>Tổng tiền:</strong> {{ number_format((float) $order->total_price, 2) }}</p>
        <p><strong>Trạng thái hiện tại:</strong> {{ \App\Models\Order::labelForStatus($order->status) }}</p>
        <p><strong>Ngày tạo:</strong> {{ $order->created_at?->format('Y-m-d H:i') }}</p>

        <form method="post" action="{{ route('admin.orders.update', $order) }}" class="a-shared-pagination-top">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>
                            {{ \App\Models\Order::labelForStatus($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="actions">
                <button class="btn primary" type="submit">Cập nhật trạng thái</button>
                <a class="btn muted" href="{{ route('admin.orders.show', $order) }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection


