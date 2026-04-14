@extends('admin.layouts.app')

@section('title', 'Cập Nhật Danh Mục')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Cập nhật danh mục: {{ $category->name }}</h1>
            <p class="subtitle">Chỉnh sửa thông tin danh mục.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.categories.show', $category) }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')

            @include('admin.categories._form', ['category' => $category])

            <div class="actions">
                <button class="btn primary" type="submit">Cập nhật</button>
                <a class="btn muted" href="{{ route('admin.categories.show', $category) }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
