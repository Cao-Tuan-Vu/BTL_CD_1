@extends('admin.layouts.app')

@section('title', 'Cập Nhật Đánh Giá')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Cập nhật đánh giá #{{ $review->id }}</h1>
            <p class="subtitle">Chỉnh sửa số sao hoặc nội dung đánh giá.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.reviews.show', $review) }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.reviews.update', $review) }}">
            @csrf
            @method('PUT')

            @include('admin.reviews._form', ['review' => $review, 'products' => $products, 'users' => $users])

            <div class="actions">
                <button class="btn primary" type="submit">Cập nhật</button>
                <a class="btn muted" href="{{ route('admin.reviews.show', $review) }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
