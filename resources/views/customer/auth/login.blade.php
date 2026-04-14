@extends('customer.layouts.app')

@section('title', 'Đăng Nhập Tài Khoản')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/auth/login.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Đăng nhập tài khoản</h1>
            <p class="c-shared-subtitle">Đăng nhập để thanh toán, quản lý hồ sơ và theo dõi đơn hàng.</p>
        </div>
    </div>

    <section class="card c-auth-login-auth-card">
        <form method="post" action="{{ route('customer.login.submit') }}" autocomplete="on">
            @csrf

            <div class="form-field">
                <label for="customer_email">Email</label>
                <input id="customer_email" type="email" name="email" value="{{ old('email') }}" autocomplete="section-customer username" autocapitalize="off" spellcheck="false" required>
            </div>

            <div class="form-field">
                <label for="customer_password">Mật khẩu</label>
                <input id="customer_password" type="password" name="password" autocomplete="section-customer current-password" required>
            </div>

            <label class="c-auth-login-block">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    Ghi nhớ đăng nhập
                </label>

            <div class="c-auth-login-block-2">
                <a href="{{ route('customer.password.request') }}" class="c-auth-login-block-3">Quên mật khẩu?</a>
            </div>

            <div class="actions">
                <button class="btn primary" type="submit">Đăng nhập</button>
                <a class="btn muted" href="{{ route('customer.register') }}">Tạo tài khoản mới</a>
            </div>

        </form>
    </section>
@endsection



