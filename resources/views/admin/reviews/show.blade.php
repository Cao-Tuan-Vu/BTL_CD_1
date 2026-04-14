@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đánh Giá')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Đánh giá{{ $review->id }}</h1>
            <p class="subtitle">Thông tin chi tiết đánh giá.</p>
        </div>
        <div class="actions">
            <a class="btn muted" href="{{ route('admin.reviews.index') }}">Quay lại</a>
        </div>
    </div>

    <section class="card">
        <p><strong>Sản phẩm:</strong> {{ $review->product?->name ?? 'Không có' }}</p>
        <p><strong>Người đánh giá:</strong> {{ $review->user?->name ?? 'Không có' }} ({{ $review->user?->email ?? 'Không có' }})</p>
        <p><strong>Số sao:</strong> {{ $review->rating }}/5</p>
        <p><strong>Nội dung:</strong> {{ $review->comment ?: 'Không có bình luận.' }}</p>
        <p><strong>Ngày tạo:</strong> {{ $review->created_at?->format('Y-m-d H:i') }}</p>
    </section>
@endsection
