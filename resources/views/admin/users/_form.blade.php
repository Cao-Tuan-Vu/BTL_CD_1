@php
    /** @var \App\Models\User|null $user */
    $user = $user ?? null;
@endphp

<div class="form-row">
    <div class="form-field">
        <label for="name">Họ tên</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user?->name) }}" required>
    </div>
    <div class="form-field">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" required>
    </div>
</div>

<div class="form-field">
    <label for="role">Vai trò</label>
    <select id="role" name="role" required>
        <option value="admin" @selected(old('role', $user?->role ?? 'customer') === 'admin')>Quản trị</option>
        <option value="customer" @selected(old('role', $user?->role ?? 'customer') === 'customer')>Khách hàng</option>
    </select>
</div>

<div class="form-row">
    <div class="form-field">
        <label for="password">
            Mật khẩu
            @if ($user)
                (để trống nếu không đổi)
            @endif
        </label>
        @if ($user)
            <input id="password" type="password" name="password" placeholder="Không bắt buộc">
        @else
            <input id="password" type="password" name="password" required>
        @endif
    </div>

    <div class="form-field">
        <label for="password_confirmation">Nhập lại mật khẩu</label>
        @if ($user)
            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Không bắt buộc">
        @else
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        @endif
    </div>
</div>
