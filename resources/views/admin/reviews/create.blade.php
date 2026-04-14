@extends('admin.layouts.app')

@section('title', 'Tạo Đánh Giá')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Tạo đánh giá</h1>
            <p class="subtitle">Thêm một đánh giá cho sản phẩm.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.reviews.index') }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.reviews.store') }}">
            @csrf

            @include('admin.reviews._form', ['products' => $products, 'users' => $users])

            <div class="actions">
                <button class="btn primary" type="submit">Lưu</button>
                <a class="btn muted" href="{{ route('admin.reviews.index') }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
