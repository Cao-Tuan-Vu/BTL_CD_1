@extends('admin.layouts.auth')

@section('title', 'Đăng Nhập Quản Trị')
@section('meta_description', 'Đăng nhập khu vực quản trị HomeSpace để quản lý sản phẩm, đơn hàng và người dùng.')

@section('content')
    <main class="admin-auth-card">
        <h1 class="title">Đăng nhập quản trị</h1>
        <p class="subtitle">Đăng nhập để truy cập quản trị hệ thống.</p>

        <form method="post" action="{{ route('admin.login.submit') }}" autocomplete="on">
            @csrf

            <div class="form-field">
                <label for="admin_email">Email</label>
                <input id="admin_email" type="email" name="email" value="{{ old('email') }}" autocomplete="section-admin username" autocapitalize="off" spellcheck="false" required>
            </div>

            <div class="form-field">
                <label for="admin_password">Mật khẩu</label>
                <input id="admin_password" type="password" name="password" autocomplete="section-admin current-password" required>
            </div>

            <label class="remember">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Ghi nhớ đăng nhập
            </label>

            <button class="btn" type="submit">Đăng nhập</button>
        </form>
    </main>
@endsection
