@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin/pages/products/_form.css') }}">
    @endpush
@endonce

@php
    /** @var \App\Models\Product|null $product */
    $product = $product ?? null;
    $mainImageUrl = $product?->image_url;
    $galleryImageUrls = $product?->gallery_image_urls ?? [];
@endphp

<div class="form-row">
    <div class="form-field">
        <label for="name">Tên sản phẩm</label>
        <input id="name" type="text" name="name" value="{{ old('name', $product?->name) }}" required>
    </div>
    <div class="form-field">
        <label for="category_id">Danh mục</label>
        <select id="category_id" name="category_id" required>
            <option value="">Chọn danh mục</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-field">
    <label for="description">Mô tả</label>
    <textarea id="description" name="description">{{ old('description', $product?->description) }}</textarea>
</div>

<div class="form-row">
    <div class="form-field">
        <label for="image">Ảnh đại diện (tối đa 4MB)</label>
        <input id="image" type="file" name="image" accept="image/*">

        @if ($mainImageUrl)
            <a href="{{ $mainImageUrl }}" target="_blank" rel="noopener">
                <img src="{{ $mainImageUrl }}" alt="Ảnh đại diện {{ $product?->name ?? 'sản phẩm' }}" class="a-products-_form-product-image-sm" decoding="async">
            </a>
        @endif
    </div>

    <div class="form-field">
        <label for="gallery_images">Bộ ảnh chi tiết (tối đa 8 ảnh)</label>
        <input id="gallery_images" type="file" name="gallery_images[]" accept="image/*" multiple>

        @if (! empty($galleryImageUrls))
            <div class="a-products-_form-block">
                @foreach ($galleryImageUrls as $galleryImageUrl)
                    <a href="{{ $galleryImageUrl }}" target="_blank" rel="noopener">
                        <img src="{{ $galleryImageUrl }}" alt="Ảnh chi tiết {{ $product?->name ?? 'sản phẩm' }}" class="a-products-_form-gallery-image-sm" loading="lazy" decoding="async">
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="form-row">
    <div class="form-field">
        <label for="price">Giá</label>
        <input id="price" type="number" min="0.01" step="0.01" name="price" value="{{ old('price', $product?->price) }}" required>
    </div>
    <div class="form-field">
        <label for="stock">Số lượng tồn</label>
        <input id="stock" type="number" min="0" step="1" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" required>
    </div>
</div>

<div class="form-field">
    <label for="status">Trạng thái</label>
    <select id="status" name="status">
        <option value="active" @selected(old('status', $product?->status ?? 'active') === 'active')>Đang bán</option>
        <option value="inactive" @selected(old('status', $product?->status) === 'inactive')>Ngừng bán</option>
    </select>
</div>



