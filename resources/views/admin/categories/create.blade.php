@extends('admin.layouts.app')

@section('title', 'Tạo Danh Mục')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Tạo danh mục</h1>
            <p class="subtitle">Thêm danh mục để nhom các sản phẩm.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.categories.index') }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.categories.store') }}">
            @csrf
            @include('admin.categories._form')

            <div class="actions">
                <button class="btn primary" type="submit">Lưu</button>
                <a class="btn muted" href="{{ route('admin.categories.index') }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
