@php
    /** @var \App\Models\Review|null $review */
    $review = $review ?? null;
    $isEdit = $review !== null;
@endphp

@if (!$isEdit)
    <div class="form-row">
        <div class="form-field">
            <label for="product_id">Sản phẩm</label>
            <select id="product_id" name="product_id" required>
                <option value="">Chọn sản phẩm</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-field">
            <label for="user_id">Người dùng</label>
            <select id="user_id" name="user_id" required>
                <option value="">Chọn người dùng</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@else
    <div class="form-row">
        <div class="form-field">
            <label>Sản phẩm</label>
            <input type="text" value="{{ $review->product?->name ?? 'Không có' }}" disabled>
        </div>
        <div class="form-field">
            <label>Người dùng</label>
            <input type="text" value="{{ $review->user?->name ?? 'Không có' }}" disabled>
        </div>
    </div>
@endif

<div class="form-field">
    <label for="rating">Số sao</label>
    <select id="rating" name="rating" required>
        @for ($i = 1; $i <= 5; $i++)
            <option value="{{ $i }}" @selected(old('rating', $review?->rating) == $i)>{{ $i }}</option>
        @endfor
    </select>
</div>

<div class="form-field">
    <label for="comment">Nội dung đánh giá</label>
    <textarea id="comment" name="comment">{{ old('comment', $review?->comment) }}</textarea>
</div>
