@extends('admin.layouts.app')

@section('title', 'Cập Nhật Người Dùng')

@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Cập nhật người dùng #{{ $user->id }}</h1>
            <p class="subtitle">Chỉnh sửa thông tin tài khoản và vai trò.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.users.show', $user) }}">Quay lại</a>
    </div>

    <section class="card">
        <form method="post" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            @include('admin.users._form', ['user' => $user])

            <div class="actions">
                <button class="btn primary" type="submit">Cập nhật</button>
                <a class="btn muted" href="{{ route('admin.users.show', $user) }}">Hủy</a>
            </div>
        </form>
    </section>
@endsection
