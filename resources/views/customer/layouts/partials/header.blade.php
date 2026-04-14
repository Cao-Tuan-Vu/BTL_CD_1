<header class="topbar">
    <div class="topbar-inner">
        <div class="navbar">
            <div class="nav-top">
                <div class="nav-top-left">
                    <a class="brand-link" href="{{ route('customer.home') }}" aria-label="Trang chủ HomeSpace">
                        <img src="{{ asset('images/logoHomeSpace.png') }}" alt="Logo HomeSpace" class="logo">
                    </a>
                </div>

                <nav class="nav nav-main" aria-label="Menu chính">
                    <a class="{{ request()->routeIs('customer.home') ? 'active' : '' }}" href="{{ route('customer.home') }}">Trang chủ</a>
                    <a class="{{ request()->routeIs('customer.about') ? 'active' : '' }}" href="{{ route('customer.about') }}">Giới thiệu</a>
                    <a class="{{ request()->routeIs('customer.contact') ? 'active' : '' }}" href="{{ route('customer.contact') }}">Liên hệ</a>
                    <a class="{{ request()->routeIs('customer.products.*') ? 'active' : '' }}" href="{{ route('customer.products.index') }}">Sản phẩm</a>
                    @if ($isCustomerAuthenticated)
                        <a class="{{ request()->routeIs('customer.orders.*') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Đơn hàng</a>
                    @endif
                </nav>

                <div class="nav-right">
                    <a class="nav-link-cart {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}" href="{{ route('customer.cart.show') }}" aria-label="Giỏ hàng">
                        <svg class="nav-icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                            <path d="M3 4h2l1.6 9.2a2 2 0 0 0 2 1.7h8.4a2 2 0 0 0 2-1.5L21 7H7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                            <circle cx="10" cy="19" r="1.6" fill="currentColor"/>
                            <circle cx="17" cy="19" r="1.6" fill="currentColor"/>
                        </svg>
                        <span class="sr-only">Giỏ hàng</span>
                        <span class="nav-badge">{{ $customerCartCount }}</span>
                    </a>

                    <a
                        class="nav-icon-link nav-link-profile {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}"
                        href="{{ $isCustomerAuthenticated ? route('customer.profile.show') : route('customer.login') }}"
                        aria-label="{{ $isCustomerAuthenticated ? 'Hồ sơ' : 'Tài khoản' }}"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="8" r="3.6" fill="none" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M4.5 19.2c1.7-3 4.4-4.6 7.5-4.6s5.8 1.6 7.5 4.6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                        </svg>
                        <span class="sr-only">Hồ sơ</span>
                    </a>

                    <nav class="nav nav-auth" aria-label="Tài khoản và thanh toán">
                        @if ($isCustomerAuthenticated)
                            <form method="post" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit" class="nav-btn">Đăng xuất</button>
                            </form>
                        @else
                            <a class="{{ request()->routeIs('customer.login') ? 'active' : '' }}" href="{{ route('customer.login') }}">Đăng nhập</a>
                            <a class="{{ request()->routeIs('customer.register') ? 'active' : '' }}" href="{{ route('customer.register') }}">Đăng ký</a>
                        @endif
                    </nav>
                </div>
            </div>

            <div class="nav-bottom">
                <form class="nav-search" method="get" action="{{ route('customer.products.index') }}" role="search" aria-label="Tìm kiếm sản phẩm">
                    <input
                        class="nav-search-input"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Tìm sản phẩm..."
                        aria-label="Tìm kiếm sản phẩm"
                    >
                    <button class="nav-search-btn" type="submit">Tìm</button>
                </form>
            </div>
        </div>
    </div>
</header>
