@extends('admin.layouts.app')

@section('title', 'Chi Tiết Danh Mục')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/categories/show.css') }}">
@endpush


@section('content')
    @php
        $productStatusLabels = [
            'active' => 'Đang bán',
            'inactive' => 'Ngưng bán',
        ];
    @endphp

    <div class="toolbar">
        <div>
            <h1 class="title">Danh mục: {{ $category->name }}</h1>
            <p class="subtitle">Thông tin danh mục và sản phẩm liên quan.</p>
        </div>
        <div class="actions">
            <a class="btn muted" href="{{ route('admin.categories.index') }}">Quay lại</a>
            <a class="btn primary" href="{{ route('admin.categories.edit', $category) }}">Chỉnh sửa</a>
        </div>
    </div>

    <section class="card a-shared-section-gap">
        <p><strong>Tên:</strong> {{ $category->name }}</p>
        <p><strong>Tổng sản phẩm:</strong> {{ $category->products_count ?? 0 }}</p>
        <p><strong>Ngày tạo:</strong> {{ $category->created_at?->format('Y-m-d H:i') }}</p>
    </section>

    <section class="card">
        <h2 class="title a-shared-title-gap">Sản phẩm trong danh mục</h2>
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td data-label="STT">{{ $loop->iteration }}</td>
                    <td data-label="Tên">{{ $product->name }}</td>
                    <td data-label="Giá">{{ number_format((float) $product->price, 2) }}</td>
                    <td data-label="Tồn kho">{{ $product->stock }}</td>
                    <td data-label="Trạng thái">{{ $productStatusLabels[$product->status] ?? $product->status }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <a class="btn muted" href="{{ route('admin.products.show', $product) }}">Xem</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không có sản phẩm nào trong danh mục này.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $products->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection


