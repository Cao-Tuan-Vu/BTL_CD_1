@extends('customer.layouts.app')

@section('title', 'Liên Hệ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/contact.css') }}">
@endpush


@section('content')
    @php
        $statusLabels = [
            'pending' => 'Chờ phản hồi',
            'replied' => 'Đã phản hồi',
        ];
    @endphp

    <section class="hero">
        <h1>Liên hệ với chúng tôi</h1>
        <p>
            Nếu bạn cần tư vấn sản phẩm, hỗ trợ đơn hàng hoặc phản hồi dịch vụ, vui lòng để lại thông tin.
            Đội ngũ sẽ phản hồi trong thời gian sớm nhất.
        </p>
    </section>

    <section class="grid two">
        <article class="card">
            <h2>Gửi yêu cầu</h2>

            @if ($customer && $customer->role === 'customer')
                <form action="{{ route('customer.contact.store') }}" method="post">
                    @csrf

                    <div class="form-field">
                        <label for="contact_name">Họ tên</label>
                        <input id="contact_name" type="text" name="name" value="{{ old('name', $customer->name) }}" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="form-field">
                        <label for="contact_phone">Số điện thoại</label>
                        <input id="contact_phone" type="text" inputmode="numeric" pattern="[0-9]*" name="phone" value="{{ old('phone') }}" placeholder="0901234567" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>

                    <div class="form-field">
                        <label for="contact_email">Email</label>
                        <input id="contact_email" type="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="email@example.com" required>
                    </div>

                    <div class="form-field">
                        <label for="contact_message">Nội dung</label>
                        <textarea id="contact_message" name="message" rows="5" placeholder="Mô tả nhu cầu của bạn..." required>{{ old('message') }}</textarea>
                    </div>

                    <button class="btn primary" type="submit">Gửi liên hệ</button>
                </form>
            @else
                <div class="c-contact-block">
                    Bạn cần đăng nhập tài khoản khách hàng để gửi liên hệ và nhận phản hồi từ quản trị viên.
                </div>

                <div class="actions">
                    <a class="btn primary" href="{{ route('customer.login') }}">Đăng nhập khách hàng</a>
                    <a class="btn muted" href="{{ route('customer.register') }}">Đăng ký tài khoản</a>
                </div>
            @endif
        </article>

        <article class="card">
            <h2>Thông tin hỗ trợ</h2>

            <p class="c-shared-meta-row"><strong>Hotline:</strong> 0901234567</p>
            <p class="c-shared-meta-row"><strong>Email:</strong> support@noithatcd1.vn</p>
            <p class="c-shared-meta-row"><strong>Địa chỉ:</strong> 123 Nguyễn Trãi, Quận 1, TP.HCM</p>
            <p class="c-shared-meta-row"><strong>Giờ làm việc:</strong> 08:00 - 21:00 (Thứ 2 - Chủ nhật)</p>

            <div class="c-contact-block-2">
                Gợi ý: Bạn có thể đính kèm mã đơn hàng trong nội dung để đội ngũ hỗ trợ nhanh hơn.
            </div>
        </article>
    </section>

    @if ($contactHistory)
        <section class="card c-shared-actions-top">
            <h2 class="c-shared-card-title">Lịch sử liên hệ của bạn</h2>

            <table>
                <thead>
                <tr>
                    <th>Ngày gửi</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Phản hồi admin</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($contactHistory as $contact)
                    <tr>
                        <td data-label="Ngày gửi">{{ $contact->created_at?->format('Y-m-d H:i') }}</td>
                        <td data-label="Nội dung">{{ $contact->message }}</td>
                        <td data-label="Trạng thái">
                            @if ($contact->status === 'replied')
                                <span class="badge ok">{{ $statusLabels[$contact->status] ?? $contact->status }}</span>
                            @else
                                <span class="badge warn">{{ $statusLabels[$contact->status] ?? $contact->status }}</span>
                            @endif
                        </td>
                        <td data-label="Phản hồi admin">
                            @if ($contact->admin_response)
                                {{ $contact->admin_response }}
                                @if ($contact->responded_at)
                                    <div class="c-contact-block-3">
                                        {{ $contact->responded_at->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            @else
                                Chưa có phản hồi.
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Bạn chưa gửi liên hệ nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="c-contact-pagination-top">
                {{ $contactHistory->withQueryString()->links('components.pagination') }}
            </div>
        </section>
    @endif
@endsection


