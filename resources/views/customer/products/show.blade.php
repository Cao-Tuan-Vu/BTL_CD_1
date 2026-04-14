@extends('customer.layouts.app')

@section('title', 'Chi Tiết Sản Phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/products/show.css') }}">
@endpush


@section('content')
    @php
        $galleryImages = collect($product->gallery_image_urls ?? [])
            ->filter()
            ->values();

        $allImages = $galleryImages
            ->prepend($product->image_url)
            ->filter()
            ->unique()
            ->values();
    @endphp

    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">{{ $product->name }}</h1>
            <p class="c-shared-subtitle">Danh mục: {{ $product->category?->name ?? 'Chưa phân loại' }}</p>
        </div>
        <a class="btn muted" href="{{ route('customer.products.index') }}">Về danh mục</a>
    </div>

    <section class="grid two c-shared-section-gap">
        <article class="card">
            @if ($allImages->isNotEmpty())
                <img src="{{ $allImages->first() }}" alt="{{ $product->name }}" class="c-products-show-block-2" decoding="async">

                @if ($allImages->skip(1)->isNotEmpty())
                    <div class="c-products-show-gallery-grid">
                        @foreach ($allImages as $imageUrl)
                            <a href="{{ $imageUrl }}" target="_blank" rel="noopener">
                                <img src="{{ $imageUrl }}" alt="Ảnh chi tiết {{ $product->name }}" class="c-products-show-block-3" loading="lazy" decoding="async">
                            </a>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="c-products-show-placeholder">
                    Chưa có hình sản phẩm
                </div>
            @endif
        </article>

        <article class="card">
            <p class="price c-products-show-block">{{ number_format((float) $product->price, 2) }}</p>
            <p class="c-products-show-block-4">
                {{ (int) $product->reviews_count }} đánh giá
                @if ($product->reviews_avg_rating)
                    · {{ number_format((float) $product->reviews_avg_rating, 1) }}/5
                @endif
            </p>
            <p class="c-shared-meta-row"><strong>Trạng thái:</strong>
                @if ($product->status === 'active')
                    <span class="badge ok">Đang bán</span>
                @else
                    <span class="badge stop">Ngừng bán</span>
                @endif
            </p>
            <p class="c-shared-meta-row"><strong>Tồn kho:</strong> {{ $product->stock }}</p>
            <p class="c-shared-meta-row"><strong>Ngày cập nhật:</strong> {{ $product->updated_at?->format('Y-m-d H:i') }}</p>
            <p class="c-products-show-block-5"><strong>Mô tả</strong></p>
            <p class="c-products-show-block-6">{{ $product->description ?: 'Không có mô tả.' }}</p>

            @if ((int) $product->stock > 0)
                <form class="actions" method="post" action="{{ route('customer.cart.add', $product) }}">
                    @csrf
                    <input type="number" name="quantity" min="1" max="{{ max(1, (int) $product->stock) }}" value="1" class="c-products-show-qty-input">
                    <x-cart-add-icon-button />
                    <a class="btn muted" href="{{ route('customer.cart.show') }}">Đến giỏ hàng</a>
                </form>
            @else
                <span class="badge stop">Hết hàng</span>
            @endif
        </article>
    </section>

    @if (auth()->check() && auth()->user()?->role === 'customer')
        <section class="card c-shared-section-gap">
            <h2 class="c-products-show-block-7">Gửi đánh giá của bạn</h2>

            <form method="post" action="{{ route('customer.products.reviews.store', $product) }}">
                @csrf

                <div class="form-row">
                    <div class="form-field">
                        <label for="rating">Số sao</label>
                        <select id="rating" name="rating" required>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected((int) old('rating', 5) === $i)>{{ $i }}/5</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="comment">Nhận xét</label>
                        <input id="comment" type="text" name="comment" value="{{ old('comment') }}" placeholder="Chia sẻ trải nghiệm của bạn...">
                    </div>
                </div>

                <button class="btn primary" type="submit">Gửi đánh giá</button>
            </form>
        </section>
    @elseif (! auth()->check())
        <section class="card c-shared-section-gap">
            <p class="c-shared-muted">
                Vui lòng đăng nhập tài khoản khách hàng để gửi đánh giá sản phẩm.
            </p>
        </section>
    @endif

    <section class="card">
        <h2 class="c-products-show-block-7">Đánh giá mới nhất ({{ (int) $product->reviews_count }})</h2>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Số sao</th>
                <th>Nội dung</th>
                <th>Ngày tạo</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($product->reviews as $review)
                <tr>
                    <td data-label="ID">#{{ $review->id }}</td>
                    <td data-label="Người dùng">{{ $review->user?->name ?? 'Không có' }}</td>
                    <td data-label="Số sao">{{ $review->rating }}/5</td>
                    <td data-label="Nội dung">{{ $review->comment ?: 'Không có bình luận' }}</td>
                    <td data-label="Ngày tạo">{{ $review->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Chưa có đánh giá.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection



