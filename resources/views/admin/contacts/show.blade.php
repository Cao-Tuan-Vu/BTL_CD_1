@extends('admin.layouts.app')

@section('title', 'Chi Tiết Liên Hệ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/contacts/show.css') }}">
@endpush


@section('content')
    <div class="toolbar">
        <div>
            <h1 class="title">Liên hệ #{{ $contact->id }}</h1>
            <p class="subtitle">Xem nội dung liên hệ và phản hồi cho khách hàng.</p>
        </div>
        <a class="btn muted" href="{{ route('admin.contacts.index') }}">Quay lại</a>
    </div>

    <section class="grid two a-shared-section-gap">
        <article class="card">
            <p><strong>Họ tên:</strong> {{ $contact->name }}</p>
            <p><strong>Email:</strong> {{ $contact->email }}</p>
            <p><strong>Số điện thoại:</strong> {{ $contact->phone }}</p>
            <p>
                <strong>Trạng thái:</strong>
                @if ($contact->status === 'replied')
                    <span class="badge ok">Đã phản hồi</span>
                @else
                    <span class="badge warn">Chờ phản hồi</span>
                @endif
            </p>
            <p><strong>Ngày gửi:</strong> {{ $contact->created_at?->format('Y-m-d H:i') }}</p>
            @if ($contact->responder)
                <p><strong>Người phản hồi:</strong> {{ $contact->responder->name }}</p>
            @endif
            @if ($contact->responded_at)
                <p><strong>Thời gian phản hồi:</strong> {{ $contact->responded_at?->format('Y-m-d H:i') }}</p>
            @endif
        </article>

        <article class="card">
            <h2 class="title a-shared-title-sm">Nội dung từ khách hàng</h2>
            <div class="a-contacts-show-multiline">{{ $contact->message }}</div>
        </article>
    </section>

    <section class="card a-shared-section-gap">
        <h2 class="title a-shared-title-sm">Phản hồi từ quản trị viên</h2>

        @if ($contact->admin_response)
            <div class="a-contacts-show-block">
                {{ $contact->admin_response }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.contacts.reply', $contact) }}">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="admin_response">Nội dung phản hồi</label>
                <textarea id="admin_response" name="admin_response" rows="5" required>{{ old('admin_response', $contact->admin_response) }}</textarea>
            </div>

            <button class="btn primary" type="submit">Gửi phản hồi</button>
        </form>
    </section>
@endsection



