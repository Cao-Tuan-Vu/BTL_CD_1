@extends('customer.layouts.app')

@section('title', 'Đăng Ký Tài Khoản')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/auth/register.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="c-shared-title">Đăng ký tài khoản</h1>
            <p class="c-shared-subtitle">Trang này chỉ dành cho khách hàng mới chưa có tài khoản.</p>
        </div>
    </div>

    <section class="card c-shared-auth-card">
        <form method="post" action="{{ route('customer.register.submit') }}" autocomplete="on">
            @csrf

            <div class="form-row">
                <div class="form-field">
                    <label for="register_name">Họ tên</label>
                    <input id="register_name" type="text" name="name" value="{{ old('name') }}" autocomplete="section-customer name" required>
                </div>

                <div class="form-field">
                    <label for="register_email">Email</label>
                    <input id="register_email" type="email" name="email" value="{{ old('email') }}" autocomplete="section-customer email" autocapitalize="off" spellcheck="false" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="register_password">Mật khẩu</label>
                    <input id="register_password" type="password" name="password" autocomplete="section-customer new-password" required>
                </div>

                <div class="form-field">
                    <label for="register_password_confirmation">Nhập lại mật khẩu</label>
                    <input id="register_password_confirmation" type="password" name="password_confirmation" autocomplete="section-customer new-password" required>
                </div>
            </div>

            <p class="c-auth-register-block">
                Mật khẩu phải có tối thiểu 6 ký tự, gồm chữ hoa, chữ thường, chữ số và ký tự đặc biệt.
            </p>

            <div class="actions">
                <button class="btn primary" type="submit">Đăng ký</button>
                <a class="btn muted" href="{{ route('customer.login') }}">Đã có tài khoản? Đăng nhập</a>
            </div>
        </form>
    </section>
@endsection



