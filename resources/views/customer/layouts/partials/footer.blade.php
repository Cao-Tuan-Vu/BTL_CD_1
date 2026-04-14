<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <img src="{{ asset('images/logoHomeSpace.png') }}" alt="Logo HomeSpace" class="logo"><br>
            <p class="footer-text">Giải pháp nội thất cho phòng khách, phòng ngủ và không gian làm việc hiện đại.</p>
        </div>

        <div class="footer-links-block">
            <div class="footer-links">
                <a class="footer-link" href="{{ route('customer.home') }}">Trang chủ</a>
                <a class="footer-link" href="{{ route('customer.about') }}">Giới thiệu</a>
                <a class="footer-link" href="{{ route('customer.contact') }}">Liên hệ</a>
                <a class="footer-link" href="{{ route('customer.products.index') }}">Sản phẩm</a>
            </div>

            <div class="footer-contact">
                <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. HN</p>
                <p>Hotline: 0901234567 | Email: caotuanvu396@gmail.com</p>
            </div>
        </div>

        <div class="footer-copy">
            <p>&copy; 2026 HomeSpace. All rights reserved.</p>
        </div>
    </div>
</footer>
