@extends('admin.layouts.app')

@section('title', 'Cập Nhật Sản Phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/products/edit.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Cập nhật sản phẩm: {{ $product->name }}</h1>
            <p class="subtitle">Chỉnh sửa thông tin sản phẩm.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.products.show', $product) }}">Quay lại</a>
    </div>

    @if ($product->image_path || ! empty($product->gallery_images))
        <section class="card a-shared-section-gap">
            <h2 class="title a-products-edit-block">Xóa ảnh hiện tại</h2>

            <div class="actions">
                @if ($product->image_path)
                    <form method="post" action="{{ route('admin.products.main-image.destroy', $product) }}" onsubmit="return confirm('Xóa ảnh đại diện hiện tại?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn danger">Xóa ảnh đại diện</button>
                    </form>
                @endif

                @if (! empty($product->gallery_images))
                    <form method="post" action="{{ route('admin.products.gallery-images.destroy', $product) }}" onsubmit="return confirm('Xóa toàn bộ bộ ảnh chi tiết hiện tại?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn danger">Xóa bộ ảnh chi tiết</button>
                    </form>
                @endif
            </div>

            <p class="a-products-edit-block-2">
                Sau khi xóa, bạn có thể tải ảnh mới ngay trong form cập nhật bên dưới.
            </p>
        </section>
    @endif

    <section class="card">
        <form method="post" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('admin.products._form', ['categories' => $categories, 'product' => $product])

            <div class="actions">
                <button type="submit" class="btn primary">Cập nhật</button>
                <a class="btn muted" href="{{ route('admin.products.show', $product) }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection



