@extends('customer.layouts.app')

@section('title', 'Danh Mục Sản Phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/products/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Danh mục sản phẩm</h1>
            <p class="c-shared-subtitle">Xem nội thất đang bán và lọc theo danh mục.</p>
        </div>
    </div>

    <section class="card c-shared-section-gap">
        <form method="get" action="{{ route('customer.products.index') }}" class="toolbar c-products-index-block">
            <div class="form-field c-products-index-block-2">
                <label for="q">Tìm kiếm</label>
                <input id="q" type="text" name="q" placeholder="Tên hoặc mô tả" value="{{ $filters['q'] ?? '' }}">
            </div>

            <div class="form-field c-products-index-block-3">
                <label for="category_id">Danh mục</label>
                <select id="category_id" name="category_id">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-field c-products-index-block-4">
                <label for="min_price">Giá từ</label>
                <input id="min_price" type="number" step="0.01" min="0" name="min_price" value="{{ $filters['min_price'] ?? '' }}">
            </div>

            <div class="form-field c-products-index-block-4">
                <label for="max_price">Đến giá</label>
                <input id="max_price" type="number" step="0.01" min="0" name="max_price" value="{{ $filters['max_price'] ?? '' }}">
            </div>

            <div class="actions">
                <button class="btn primary" type="submit">Áp dụng</button>
                <a class="btn muted" href="{{ route('customer.products.index') }}">Đặt lại</a>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="product-grid">
            @forelse ($products as $product)
                <article class="product-card">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="c-products-index-product-image" loading="lazy" decoding="async">
                    @else
                        <div class="c-products-index-placeholder">
                            Chưa có ảnh
                        </div>
                    @endif

                    <div class="product-meta">
                        <span class="badge info">{{ $product->category?->name ?? 'Chưa phân loại' }}</span>
                        <span>Tồn kho: {{ $product->stock }}</span>
                    </div>

                    <h3>{{ $product->name }}</h3>
                    <p class="c-products-index-block-5">
                        {{ \Illuminate\Support\Str::limit($product->description ?? 'Không có mô tả', 90) }}
                    </p>
                    <p class="c-products-index-block-6">
                        {{ (int) $product->reviews_count }} đánh giá
                        @if ($product->reviews_avg_rating)
                            · {{ number_format((float) $product->reviews_avg_rating, 1) }}/5
                        @endif
                    </p>
                    <p class="price">{{ number_format((float) $product->price, 2) }}</p>

                    <div class="actions">
                        <a class="btn muted" href="{{ route('customer.products.show', $product) }}">Chi tiết</a>

                        @if ((int) $product->stock > 0)
                            <form class="actions" method="post" action="{{ route('customer.cart.add', $product) }}">
                                @csrf
                                <input type="number" name="quantity" min="1" max="{{ max(1, (int) $product->stock) }}" value="1" class="c-products-index-qty-input">
                                <x-cart-add-icon-button />
                            </form>
                        @else
                            <span class="badge stop">Hết hàng</span>
                        @endif
                    </div>
                </article>
            @empty
                <p>Không có sản phẩm phù hợp bộ lọc.</p>
            @endforelse
        </div>

        <div class="c-shared-actions-top">
            {{ $products->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection



