@extends('admin.layouts.app')

@section('title', 'Sản Phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/products/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Sản phẩm</h1>
            <p class="subtitle">Quản lý danh mục sản phẩm với bộ lọc và phân trang.</p>
        </div>
        <a class="btn primary" href="{{ route('admin.products.create') }}">Tạo sản phẩm</a>
    </div>

    <section class="card a-shared-section-gap">
        <form method="get" action="{{ route('admin.products.index') }}">
            <input type="text" name="q" placeholder="Tìm theo tên/mô tả" value="{{ $filters['q'] ?? '' }}">
            <select name="category_id">
                <option value="">Tất cả danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Đang bán</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Ngừng bán</option>
            </select>
            <input type="number" step="0.01" min="0" name="min_price" placeholder="Giá từ" value="{{ $filters['min_price'] ?? '' }}">
            <input type="number" step="0.01" min="0" name="max_price" placeholder="Đến giá" value="{{ $filters['max_price'] ?? '' }}">
            <button class="btn primary" type="submit">Lọc</button>
            <a class="btn muted" href="{{ route('admin.products.index') }}">Đặt lại</a>
        </form>
    </section>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Đánh giá</th>
                <th>Trạng thái</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td data-label="ID">#{{ $product->id }}</td>
                    <td data-label="Ảnh">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="a-products-index-block" loading="lazy" decoding="async">
                        @else
                            <span class="badge">Chưa có</span>
                        @endif
                    </td>
                    <td data-label="Tên">{{ $product->name }}</td>
                    <td data-label="Danh mục">{{ $product->category?->name ?? 'Không có' }}</td>
                    <td data-label="Giá">{{ number_format((float) $product->price, 2) }}</td>
                    <td data-label="Tồn kho">{{ $product->stock }}</td>
                    <td data-label="Đánh giá">
                        {{ (int) $product->reviews_count }} lượt
                        @if ($product->reviews_avg_rating)
                            <br>
                            <small>TB: {{ number_format((float) $product->reviews_avg_rating, 1) }}/5</small>
                        @endif
                    </td>
                    <td data-label="Trạng thái">
                        @if ($product->status === 'active')
                            <span class="badge ok">Đang bán</span>
                        @else
                            <span class="badge stop">Ngừng bán</span>
                        @endif
                    </td>
                    <td data-label="Tác vụ" class="text-right">
                        <div class="actions a-shared-actions-end">
                            <a class="btn muted" href="{{ route('admin.products.show', $product) }}">Xem</a>
                            <a class="btn muted" href="{{ route('admin.products.edit', $product) }}">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="post" onsubmit="return confirm('Xóa sản phẩm này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Không tìm thấy sản phẩm nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $products->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection



