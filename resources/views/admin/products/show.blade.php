@extends('admin.layouts.app')

@section('title', 'Chi Tiết Sản Phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/products/show.css') }}">
@endpush


@section('content')
    @php
        $productStatusLabels = [
            'active' => 'Đang bán',
            'inactive' => 'Ngừng bán',
        ];
        $galleryImages = collect($product->gallery_image_urls ?? [])
            ->filter()
            ->values();
    @endphp

    <div class="toolbar">
        <div>
            <h1 class="title">Sản phẩm: {{ $product->name }}</h1>
            <p class="subtitle">Thông tin chi tiết và đánh giá liên quan.</p>
        </div>
        <div class="actions">
            <a class="btn muted" href="{{ route('admin.products.index') }}">Quay lại</a>
            <a class="btn primary" href="{{ route('admin.products.edit', $product) }}">Chỉnh sửa</a>
        </div>
    </div>

    <section class="grid two a-shared-section-gap">
        <article class="card">
            <h2 class="title a-shared-title-gap">Thông tin cơ bản</h2>
            <p><strong>Tên:</strong> {{ $product->name }}</p>
            <p><strong>Danh mục:</strong> {{ $product->category?->name ?? 'Không có' }}</p>
            <p><strong>Giá:</strong> {{ number_format((float) $product->price, 2) }}</p>
            <p><strong>Tồn kho:</strong> {{ $product->stock }}</p>
            <p><strong>Trạng thái:</strong> {{ $productStatusLabels[$product->status] ?? $product->status }}</p>
            <p><strong>Lượt đánh giá:</strong> {{ (int) $product->reviews_count }}</p>
            <p>
                <strong>Điểm trung bình:</strong>
                @if ($product->reviews_avg_rating)
                    {{ number_format((float) $product->reviews_avg_rating, 1) }}/5
                @else
                    Chưa có
                @endif
            </p>
            <p><strong>Ngày tạo:</strong> {{ $product->created_at?->format('Y-m-d H:i') }}</p>
        </article>

        <article class="card">
            <h2 class="title a-shared-title-gap">Hình ảnh</h2>
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="a-products-show-block" decoding="async">
            @else
                <p class="a-products-show-block-2">Chưa có ảnh đại diện.</p>
            @endif

            @if ($galleryImages->isNotEmpty())
                <div class="a-products-show-gallery-grid">
                    @foreach ($galleryImages as $imageUrl)
                        <a href="{{ $imageUrl }}" target="_blank" rel="noopener">
                            <img src="{{ $imageUrl }}" alt="Ảnh chi tiết {{ $product->name }}" class="a-products-show-gallery-image-md" loading="lazy" decoding="async">
                        </a>
                    @endforeach
                </div>
            @endif

            <h2 class="title a-shared-title-gap">Mô tả</h2>
            <p>{{ $product->description ?: 'Không có mô tả.' }}</p>
        </article>
    </section>

    <section class="card">
        <h2 class="title a-shared-title-gap">Đánh giá ({{ (int) $product->reviews_count }})</h2>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Số sao</th>
                <th>Nội dung</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($product->reviews as $review)
                <tr>
                    <td data-label="ID">#{{ $review->id }}</td>
                    <td data-label="Người dùng">{{ $review->user?->name ?? 'Không có' }}</td>
                    <td data-label="Số sao">{{ $review->rating }}/5</td>
                    <td data-label="Nội dung">{{ $review->comment ?: 'Không có bình luận.' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Sản phẩm này chưa có đánh giá.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection



