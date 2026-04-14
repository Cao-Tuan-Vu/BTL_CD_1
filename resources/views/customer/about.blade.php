@extends('customer.layouts.app')

@section('title', 'Giới Thiệu')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/pages/about.css') }}">
@endpush


@section('content')
    <section class="hero">
        <h2>Về HomeSpace</h2>
        <p>
            HomeSpace hướng đến trải nghiệm mua sắm nội thất đơn giản, minh bạch và thân thiện.
            Chúng tôi tập trung vào sản phẩm có thiết kế hiện đại, chất lượng tốt và phù hợp không gian sống Việt Nam.
        </p>
    </section>

    <section class="grid two c-shared-section-gap">
        <article class="card">
            <h2>Sứ mệnh</h2>
            <p class="c-shared-muted">
                Mang đến giải pháp nội thất bền đẹp, dễ phối hợp, giúp khách hàng hoàn thiện không gian sống một cách nhanh chóng.
            </p>
        </article>

        <article class="card">
            <h2 >Giá trị cốt lõi</h2>
            <ul class="c-about-block-2">
                <li>Chất lượng sản phẩm rõ ràng</li>
                <li>Dịch vụ tư vấn tận tâm</li>
                <li>Giao hàng đúng hẹn</li>
                <li>Hỗ trợ khách hàng minh bạch</li>
            </ul>
        </article>
    </section>

    <section class="card">
        <div class="toolbar c-about-block">
            <h2>Thông tin nhanh</h2>
        </div>

        <div class="grid two">
            <div>
                <p class="c-about-block-3"><strong>Địa chỉ:</strong> 123 Nguyễn Trãi, Quận 1, TP.HCM</p>
                <p class="c-about-block-3"><strong>Giờ hoạt động:</strong> 08:00 - 21:00 (Thứ 2 - Chủ nhật)</p>
            </div>
            <div>
                <p class="c-about-block-3"><strong>Email:</strong> support@noithatcd1.vn</p>
                <p class="c-about-block-3"><strong>Hotline:</strong> 0901234567</p>
            </div>
        </div>
    </section>
@endsection


