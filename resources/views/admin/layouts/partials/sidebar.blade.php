<aside class="sidebar">
    <a class="sidebar-brand" href="{{ route('admin.home') }}">Admin</a>
    <p class="sidebar-sub">Quản trị hệ thống</p>

    <nav class="sidebar-nav">
        <a class="{{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">Tổng quan</a>
        <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Sản phẩm</a>
        <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Danh mục</a>
        <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Đơn hàng</a>
        <a class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">Đánh giá</a>
        <a class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">Liên hệ</a>
        <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Người dùng</a>
        <a class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.show') }}">Hồ sơ</a>
    </nav>

    <div class="sidebar-footer">
        @if (auth()->check())
            <div>
                Đang đăng nhập với <strong>{{ auth()->user()->name }}</strong>
            </div>
        @endif

        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn danger block">Đăng xuất</button>
        </form>
    </div>
</aside>
