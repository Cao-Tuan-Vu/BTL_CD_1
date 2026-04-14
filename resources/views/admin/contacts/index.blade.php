@extends('admin.layouts.app')

@section('title', 'Liên Hệ Khách Hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/contacts/index.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Liên hệ khách hàng</h1>
            <p class="subtitle">Tiếp nhận thông tin liên hệ và phản hồi cho khách hàng.</p>
        </div>
    </div>

    <section class="grid stats a-contacts-index-block">
        <article class="card">
            <div class="subtitle">Tổng liên hệ</div>
            <div class="title a-contacts-index-block-2">{{ $summary['total'] }}</div>
        </article>
        <article class="card">
            <div class="subtitle">Chờ phản hồi</div>
            <div class="title a-contacts-index-block-3">{{ $summary['pending'] }}</div>
        </article>
        <article class="card">
            <div class="subtitle">Đã phản hồi</div>
            <div class="title a-contacts-index-block-4">{{ $summary['replied'] }}</div>
        </article>
    </section>

    <section class="card a-shared-section-gap">
        <form method="get" action="{{ route('admin.contacts.index') }}">
            <select name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Chờ phản hồi</option>
                <option value="replied" @selected(($filters['status'] ?? '') === 'replied')>Đã phản hồi</option>
            </select>

            <input
                type="text"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Tìm theo tên, email, số điện thoại..."
            >

            <button class="btn primary" type="submit">Lọc</button>
            <a class="btn muted" href="{{ route('admin.contacts.index') }}">Đặt lại</a>
        </form>
    </section>

    <section class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Thông tin liên hệ</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th class="text-right">Tác vụ</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($contacts as $contact)
                <tr>
                    <td data-label="ID">#{{ $contact->id }}</td>
                    <td data-label="Khách hàng">
                        {{ $contact->name }}
                        @if ($contact->user)
                            <div class="a-contacts-index-block-5">Tài khoản: {{ $contact->user->name }}</div>
                        @endif
                    </td>
                    <td data-label="Thông tin liên hệ">
                        <div>{{ $contact->email }}</div>
                        <div>{{ $contact->phone }}</div>
                    </td>
                    <td data-label="Nội dung">{{ \Illuminate\Support\Str::limit($contact->message, 90) }}</td>
                    <td data-label="Trạng thái">
                        @if ($contact->status === 'replied')
                            <span class="badge ok">Đã phản hồi</span>
                        @else
                            <span class="badge warn">Chờ phản hồi</span>
                        @endif
                    </td>
                    <td data-label="Tác vụ" class="text-right">
                        <a class="btn muted" href="{{ route('admin.contacts.show', $contact) }}">Xem & phản hồi</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không có liên hệ nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="a-shared-pagination-top">
            {{ $contacts->withQueryString()->links('components.pagination') }}
        </div>
    </section>
@endsection



