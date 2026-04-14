@extends('admin.layouts.app')

@section('title', 'Đánh Giá')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/reviews/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Đánh giá</h1>
            <p class="subtitle">Tiếp nhận đánh giá từ khách hàng và theo dõi thống kê theo từng sản phẩm.</p>
        </div>
    </div>

    <section class="card a-shared-section-gap">
        <h2 class="title a-shared-title-sm">Thống kê đánh giá theo sản phẩm</h2>

        <table>
            <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượt đánh giá</th>
                <th>Điểm trung bình</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($productReviewStats as $product)
                <tr>
                    <td data-label="Sản phẩm">{{ $product->name }}</td>
                    <td data-label="Số lượt đánh giá">{{ $product->reviews_count }}</td>
                    <td data-label="Điểm trung bình">{{ number_format((float) ($product->reviews_avg_rating ?? 0), 1) }}/5</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Chưa có dữ liệu thống kê đánh giá.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="card a-shared-section-gap">
        <form method="get" action="{{ route('admin.reviews.index') }}">
            <select name="product_id">
                <option value="">Tất cả sản phẩm</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            <select name="user_id">
                <option value="">Tất cả người dùng</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn primary" type="submit">Loc</button>
            <a class="btn muted" href="{{ route('admin.reviews.index') }}">Đặt lại</a>
        </form>
    </section>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Người dùng</th>
                <th>Số sao</th>
                <th>Nội dung</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Sản phẩm">{{ $review->product?->name ?? 'Không có' }}</td>
                    <td data-label="Người dùng">{{ $review->user?->name ?? 'Không có' }}</td>
                    <td data-label="Số sao">{{ $review->rating }}/5</td>
                    <td data-label="Nội dung">{{ $review->comment ?: 'Không có bình luận.' }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <div class="actions a-shared-actions-end">
                            <a class="btn muted" href="{{ route('admin.reviews.show', $review) }}">Xem</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không tìm thấy đánh giá nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $reviews->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection


