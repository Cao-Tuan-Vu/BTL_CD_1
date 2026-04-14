@extends('customer.layouts.app')

@section('title', 'Thông Tin Cá Nhân')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/auth/profile.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="shared-title">Thông tin cá nhân</h1>
            <p class="c-shared-subtitle">Cập nhật thông tin cá nhân và đổi mật khẩu.</p>
        </div>
    </div>

    <section class="grid two">
        <article class="card">
            <h2 class="c-shared-card-title">Thông tin tài khoản</h2>

            <form method="post" action="{{ route('customer.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="name">Họ tên</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required>
                </div>

                <div class="form-field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}" required>
                </div>

                <button class="btn primary" type="submit">Lưu thông tin</button>
            </form>
        </article>

        <article class="card">
            <h2 class="c-shared-card-title">Đổi mật khẩu</h2>

            <form method="post" action="{{ route('customer.profile.password.update') }}">
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

                <button class="btn primary" type="submit">Cập nhật mật khẩu</button>
            </form>
        </article>
    </section>
@endsection


