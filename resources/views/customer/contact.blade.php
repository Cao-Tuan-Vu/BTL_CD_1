@extends('customer.layouts.app') // Kế thừa layout chính của customer

@section('title', 'Liên Hệ') // Tiêu đề trang

@push('styles')
    <!-- Nhúng file CSS riêng cho trang liên hệ -->
    <link rel="stylesheet" href="{{ asset('css/customer/pages/contact.css') }}">
@endpush


@section('content')
    @php
        // Map trạng thái sang nhãn hiển thị
        $statusLabels = [
            'pending' => 'Chờ phản hồi',
            'replied' => 'Đã phản hồi',
        ];
    @endphp

    <!-- Section banner đầu trang -->
    <section class="hero">
        <h1>Liên hệ với chúng tôi</h1>
        <p>
            Nếu bạn cần tư vấn sản phẩm, hỗ trợ đơn hàng hoặc phản hồi dịch vụ,
            vui lòng để lại thông tin. Đội ngũ sẽ phản hồi sớm nhất.
        </p>
    </section>

    <!-- Layout chia 2 cột -->
    <section class="grid two">
        
        <!-- FORM GỬI LIÊN HỆ -->
        <article class="card">
            <h2>Gửi yêu cầu</h2>

            <!-- Kiểm tra nếu đã đăng nhập với role customer -->
            @if ($customer && $customer->role === 'customer')
                <form action="{{ route('customer.contact.store') }}" method="post">
                    @csrf <!-- Token bảo mật CSRF -->

                    <!-- Họ tên -->
                    <div class="form-field">
                        <label for="contact_name">Họ tên</label>
                        <input id="contact_name" type="text" name="name"
                               value="{{ old('name', $customer->name) }}"
                               placeholder="Nguyễn Văn A" required>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="form-field">
                        <label for="contact_phone">Số điện thoại</label>
                        <input id="contact_phone" type="text"
                               inputmode="numeric" pattern="[0-9]*"
                               name="phone"
                               value="{{ old('phone') }}"
                               placeholder="0901234567"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="form-field">
                        <label for="contact_email">Email</label>
                        <input id="contact_email" type="email"
                               name="email"
                               value="{{ old('email', $customer->email) }}"
                               placeholder="email@example.com" required>
                    </div>

                    <!-- Nội dung -->
                    <div class="form-field">
                        <label for="contact_message">Nội dung</label>
                        <textarea id="contact_message" name="message"
                                  rows="5"
                                  placeholder="Mô tả nhu cầu của bạn..."
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <!-- Nút gửi -->
                    <button class="btn primary" type="submit">Gửi liên hệ</button>
                </form>
            @else
                <!-- Nếu chưa đăng nhập -->
                <div class="c-contact-block">
                    Bạn cần đăng nhập tài khoản khách hàng để gửi liên hệ.
                </div>

                <div class="actions">
                    <a class="btn primary" href="{{ route('customer.login') }}">Đăng nhập</a>
                    <a class="btn muted" href="{{ route('customer.register') }}">Đăng ký</a>
                </div>
            @endif
        </article>

        <!-- THÔNG TIN LIÊN HỆ -->
        <article class="card">
            <h2>Thông tin hỗ trợ</h2>

            <p><strong>Hotline:</strong> 0901234567</p>
            <p><strong>Email:</strong> support@noithatcd1.vn</p>
            <p><strong>Địa chỉ:</strong> 123 Nguyễn Trãi, Quận 1, TP.HCM</p>
            <p><strong>Giờ làm việc:</strong> 08:00 - 21:00</p>

            <div class="c-contact-block-2">
                Gợi ý: Thêm mã đơn hàng để được hỗ trợ nhanh hơn.
            </div>
        </article>
    </section>

    <!-- LỊCH SỬ LIÊN HỆ -->
    @if ($contactHistory)
        <section class="card c-shared-actions-top">
            <h2>Lịch sử liên hệ của bạn</h2>

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

                <!-- Duyệt danh sách liên hệ -->
                @forelse ($contactHistory as $contact)
                    <tr>
                        <!-- Thời gian gửi -->
                        <td>{{ $contact->created_at?->format('Y-m-d H:i') }}</td>

                        <!-- Nội dung -->
                        <td>{{ $contact->message }}</td>

                        <!-- Trạng thái -->
                        <td>
                            @if ($contact->status === 'replied')
                                <span class="badge ok">
                                    {{ $statusLabels[$contact->status] }}
                                </span>
                            @else
                                <span class="badge warn">
                                    {{ $statusLabels[$contact->status] }}
                                </span>
                            @endif
                        </td>

                        <!-- Phản hồi admin -->
                        <td>
                            @if ($contact->admin_response)
                                {{ $contact->admin_response }}

                                <!-- Thời gian phản hồi -->
                                @if ($contact->responded_at)
                                    <div>
                                        {{ $contact->responded_at->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            @else
                                Chưa có phản hồi.
                            @endif
                        </td>
                    </tr>

                @empty
                    <!-- Nếu không có dữ liệu -->
                    <tr>
                        <td colspan="4">Bạn chưa gửi liên hệ nào.</td>
                    </tr>
                @endforelse

                </tbody>
            </table>

            <!-- Phân trang -->
            <div>
                {{ $contactHistory->withQueryString()->links('components.pagination') }}
            </div>
        </section>
    @endif
@endsection