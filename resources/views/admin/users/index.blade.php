@extends('admin.layouts.app')

@section('title', 'Người Dùng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/users/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Người dùng</h1>
            <p class="subtitle">Quản lý tài khoản quản trị và khách hàng.</p>
        </div>
        <a class="btn primary" href="{{ route('admin.users.create') }}">Tạo người dùng</a>
    </div>

    <section class="card a-shared-section-gap">
        <form method="get" action="{{ route('admin.users.index') }}">
            <input type="text" name="q" placeholder="Tim theo ten/email" value="{{ $filters['q'] ?? '' }}">
            <select name="role">
                <option value="">Tất cả vai trò</option>
                <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Quản trị</option>
                <option value="customer" @selected(($filters['role'] ?? '') === 'customer')>Khách hàng</option>
            </select>
            <button class="btn primary" type="submit">Loc</button>
            <a class="btn muted" href="{{ route('admin.users.index') }}">Đặt lại</a>
        </form>
    </section>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Đơn hàng</th>
                <th>Đánh giá</th>
                <th>Ngày tạo</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
                    <td data-label="ID">#{{ $user->id }}</td>
                    <td data-label="Họ tên">{{ $user->name }}</td>
                    <td data-label="Email">{{ $user->email }}</td>
                    <td data-label="Vai trò">
                        @if ($user->role === 'admin')
                            <span class="badge info">Quản trị</span>
                        @else
                            <span class="badge ok">Khách hàng</span>
                        @endif
                    </td>
                    <td data-label="Đơn hàng">{{ $user->orders_count ?? 0 }}</td>
                    <td data-label="Đánh giá">{{ $user->reviews_count ?? 0 }}</td>
                    <td data-label="Ngày tạo">{{ $user->created_at?->format('Y-m-d') }}</td>
                    <td data-label="Tác vụ" class="text-right">
                        <div class="actions a-shared-actions-end">
                            <a class="btn muted" href="{{ route('admin.users.show', $user) }}">Xem</a>
                            <a class="btn muted" href="{{ route('admin.users.edit', $user) }}">Sửa</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="post" onsubmit="return confirm('Xóa người dùng này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Không tìm thấy người dùng nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $users->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection


