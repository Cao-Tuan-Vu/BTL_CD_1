@extends('customer.layouts.app')

@section('title', 'Quên Mật Khẩu')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/auth/forgot-password.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Quên mật khẩu</h1>
            <p class="c-shared-subtitle">Nhập email tài khoản khách hàng để lấy OTP 6 số đặt lại mật khẩu.</p>
        </div>
        <a class="btn muted" href="{{ route('customer.login') }}">Quay lại đăng nhập</a>
    </div>

    <section class="card c-auth-forgot-password-auth-card">
        <form method="post" action="{{ route('customer.password.email') }}" autocomplete="on">
            @csrf

            <div class="form-field">
                <label for="forgot_email">Email</label>
                <input
                    id="forgot_email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="section-customer email"
                    autocapitalize="off"
                    spellcheck="false"
                    required
                >
            </div>

            <div class="actions">
                <button class="btn primary" type="submit">Lấy OTP đặt lại mật khẩu</button>
                <a class="btn muted" href="{{ route('customer.register') }}">Chưa có tài khoản?</a>
            </div>
        </form>
    </section>
@endsection



