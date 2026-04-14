@extends('admin.layouts.app')

@section('title', 'Tạo Sản Phẩm')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Tạo sản phẩm</h1>
            <p class="subtitle">Thêm sản phẩm mới vào danh mục kinh doanh.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.products.index') }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form', ['categories' => $categories])

            <div class="actions">
                <button type="submit" class="btn primary">Lưu</button>
                <a class="btn muted" href="{{ route('admin.products.index') }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
