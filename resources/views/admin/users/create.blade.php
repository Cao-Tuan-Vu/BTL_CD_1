@extends('admin.layouts.app')

@section('title', 'Tạo Người Dùng')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Tạo người dùng</h1>
            <p class="subtitle">Thêm tài khoản quản trị hoặc khách hàng mới.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.users.index') }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.users.store') }}">
            @csrf

            @include('admin.users._form')

            <div class="actions">
                <button class="btn primary" type="submit">Lưu</button>
                <a class="btn muted" href="{{ route('admin.users.index') }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
