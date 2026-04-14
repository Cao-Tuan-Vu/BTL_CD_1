@extends('customer.layouts.app')

@section('title', 'Đặt Lại Mật Khẩu')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/auth/reset-password.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Đặt lại mật khẩu</h1>
            <p class="c-shared-subtitle">Nhập OTP 6 số và tạo mật khẩu mới cho tài khoản khách hàng.</p>
        </div>
        <a class="btn muted" href="{{ route('customer.login') }}">Quay lại đăng nhập</a>
    </div>

    <section class="card c-shared-auth-card">
        @if (session('password_reset_otp'))
            <div class="alert success">
                Mã OTP: <strong>{{ session('password_reset_otp') }}</strong> (hiệu lực 10 phút).
            </div>
        @endif

        <form method="post" action="{{ route('customer.password.update') }}" autocomplete="on">
            @csrf

            <div class="form-field">
                <label for="reset_email">Email</label>
                <input
                    id="reset_email"
                    type="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    autocomplete="section-customer email"
                    autocapitalize="off"
                    spellcheck="false"
                    required
                >
            </div>

            <div class="form-field">
                <label for="reset_otp">OTP 6 số</label>
                <input
                    id="reset_otp"
                    type="text"
                    name="otp"
                    value="{{ old('otp', $otp ?? '') }}"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                    autocomplete="one-time-code"
                    required
                >
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="reset_password">Mật khẩu mới</label>
                    <input id="reset_password" type="password" name="password" autocomplete="section-customer new-password" required>
                </div>

                <div class="form-field">
                    <label for="reset_password_confirmation">Nhập lại mật khẩu mới</label>
                    <input id="reset_password_confirmation" type="password" name="password_confirmation" autocomplete="section-customer new-password" required>
                </div>
            </div>

            <p class="c-auth-reset-password-block">
                Mật khẩu phải có tối thiểu 6 ký tự, gồm chữ hoa, chữ thường, chữ số và ký tự đặc biệt.
            </p>

            <button class="btn primary" type="submit">Đặt lại mật khẩu</button>
        </form>
    </section>
@endsection



