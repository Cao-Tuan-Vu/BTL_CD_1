@php
    /** @var \App\Models\Category|null $category */
    $category = $category ?? null;
@endphp

<div class="form-field">
    <label for="name">Ten danh mục</label>
    <input id="name" type="text" name="name" value="{{ old('name', $category?->name) }}" required>
</div>

<div class="form-field">
    <label for="slug">Slug</label>
    <input id="slug" type="text" name="slug" value="{{ old('slug', $category?->slug) }}" required>
</div>
