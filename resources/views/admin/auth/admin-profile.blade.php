@extends('admin.layouts.app')

@section('title', 'Hồ Sơ Quản Trị')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/auth/admin-profile.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Hồ sơ quản trị</h1>
            <p class="subtitle">Cập nhật thông tin tài khoản và mật khẩu.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.home') }}">Về tổng quan</a>
    </div>

    <section class="grid two">
        <article class="card">
            <h2 class="title a-auth-admin-profile-block">Thông tin tài khoản</h2>

            <form method="post" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="name">Họ tên</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                </div>

                <div class="form-field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                </div>

                <div class="actions">
                    <button class="btn primary" type="submit">Lưu thông tin</button>
                </div>
            </form>
        </article>

        <article class="card">
            <h2 class="title a-auth-admin-profile-block">Đổi mật khẩu</h2>

            <form method="post" action="{{ route('admin.profile.password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="current_password">Mật khẩu hiện tại</label>
                    <input id="current_password" type="password" name="current_password" required>
                </div>

                <div class="form-field">
                    <label for="password">Mật khẩu mới</label>
                    <input id="password" type="password" name="password" required>
                </div>

                <div class="form-field">
                    <label for="password_confirmation">Nhập lại mật khẩu mới</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>

                <div class="actions">
                    <button class="btn primary" type="submit">Cập nhật mật khẩu</button>
                </div>
            </form>
        </article>
    </section>
@endsection



