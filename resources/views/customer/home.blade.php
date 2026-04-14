@extends('customer.layouts.app')

@section('title', 'Trang Chủ')
@section('meta_description', 'Khám phá nội thất HomeSpace với các sản phẩm nổi bật cho phòng khách, phòng ngủ và không gian làm việc.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/home.css') }}">
@endpush

@section('content')
    @php
        $homeSlides = [
            asset('images/hero_slide1.png'),
            asset('images/hero_slide2.png'),
            asset('images/hero_slide3.png'),
            asset('images/hero_slide4.png'),
            asset('images/hero_slide5.png'),
            asset('images/hero_slide6.png'),
        ];
    @endphp

    <section
        class="hero home-hero"
        id="home-hero-slideshow"
        data-slides='@json($homeSlides)'
    >
        <div class="hero-bg bg1 active" aria-hidden="true"></div>
        <div class="hero-bg bg2" aria-hidden="true"></div>
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="hero-content">
            <h2>Kiến tạo không gian sống ấm cúng và hiện đại.</h2>
            <p>
                Khám phá bộ sưu tập nội thất được chọn lọc cho phòng ngủ, phòng khách và không gian làm việc.
            </p>

            <div class="actions home-hero-actions">
                <a class="btn primary" href="{{ route('customer.products.index') }}">Xem sản phẩm</a>
                <a class="btn muted home-cart-btn" href="{{ route('customer.cart.show') }}">Mở giỏ hàng</a>
            </div>
        </div>
    </section>

    <section class="card home-section">
        <div class="toolbar home-toolbar-tight">
            <h2>Mua sắm theo danh mục</h2>
        </div>

        <div class="chips home-chips">
            <a class="chip" href="{{ route('customer.products.index') }}">Tất cả sản phẩm</a>
            @foreach ($categories as $category)
                <a class="chip" href="{{ route('customer.products.index', ['category_id' => $category->id]) }}">{{ $category->name }}</a>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div class="toolbar">
            <div>
                <h2 class="home-featured-title">Sản phẩm nổi bật</h2>
                <p class="home-featured-subtitle">Những sản phẩm đang bán được chọn lọc mới nhất.</p>
            </div>
            <a class="btn muted" href="{{ route('customer.products.index') }}">Xem tất cả</a>
        </div>

        <div class="product-grid">
            @forelse ($featuredProducts as $product)
                <article class="product-card">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="home-product-image" loading="lazy" decoding="async">
                    @else
                        <div class="home-product-placeholder">Chưa có ảnh</div>
                    @endif

                    <div class="product-meta">
                        <span class="badge info">{{ $product->category?->name ?? 'Chưa phân loại' }}</span>
                    </div>

                    <h3>{{ $product->name }}</h3>
                    <p class="home-product-stats">
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
                                <input
                                    class="home-product-qty"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="{{ max(1, (int) $product->stock) }}"
                                    value="1"
                                >
                                <x-cart-add-icon-button />
                            </form>
                        @else
                            <span class="badge stop">Hết hàng</span>
                        @endif
                    </div>
                </article>
            @empty
                <p>Không tìm thấy sản phẩm đang bán.</p>
            @endforelse
        </div>
    </section>

    <section class="card home-section">
        <div class="toolbar">
            <div>
                <h2 class="home-featured-title">Sản phẩm khác</h2>
                <p class="home-featured-subtitle">Các sản phẩm đang bán còn lại bạn có thể quan tâm.</p>
            </div>
            <a class="btn muted" href="{{ route('customer.products.index') }}">Xem tất cả</a>
        </div>

        <div class="product-grid">
            @forelse ($otherProducts as $product)
                <article class="product-card">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="home-product-image" loading="lazy" decoding="async">
                    @else
                        <div class="home-product-placeholder">Chưa có ảnh</div>
                    @endif

                    <div class="product-meta">
                        <span class="badge info">{{ $product->category?->name ?? 'Chưa phân loại' }}</span>
                    </div>

                    <h3>{{ $product->name }}</h3>
                    <p class="home-product-stats">
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
                                <input
                                    class="home-product-qty"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="{{ max(1, (int) $product->stock) }}"
                                    value="1"
                                >
                                <x-cart-add-icon-button />
                            </form>
                        @else
                            <span class="badge stop">Hết hàng</span>
                        @endif
                    </div>
                </article>
            @empty
                <p>Không có sản phẩm khác để hiển thị.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/customer/home-slideshow.js') }}" defer></script>
@endpush
