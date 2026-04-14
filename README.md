<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<h1 align="center">🛋️ WEBSITE QUẢN LÝ & BÁN ĐỒ NỘI THẤT</h1>

<p align="center">
Hệ thống thương mại điện tử nội thất xây dựng bằng Laravel
</p>

---

## 📌 Giới thiệu

Dự án xây dựng hệ thống web **quản lý và bán đồ nội thất** với đầy đủ chức năng:

* Khách hàng mua hàng
* Admin quản lý hệ thống
* Xử lý đơn hàng, thanh toán, thống kê

---

## ⚙️ Công nghệ sử dụng

* **Backend**: Laravel
* **Database**: MySQL
* **Frontend**: Blade + Bootstrap
* **ORM**: Eloquent
* **Authentication**: Laravel Breeze / Sanctum
* **Container**: Docker

---

## 🚀 Chức năng hệ thống

### 👤 Khách hàng (User)

* Đăng ký / đăng nhập / đăng xuất
* Quên mật khẩu, cập nhật thông tin
* Xem & tìm kiếm sản phẩm
* Lọc / sắp xếp sản phẩm
* Xem chi tiết + đánh giá
* Giỏ hàng
* Đặt hàng (COD / Online)
* Quản lý đơn hàng

---

### 🛠️ Quản trị (Admin)

* Dashboard thống kê
* Quản lý danh mục
* Quản lý sản phẩm
* Quản lý đơn hàng
* Quản lý người dùng
* Quản lý đánh giá
* Thống kê nâng cao

---

### ⚙️ Hệ thống

* Authentication & Authorization
* Validation (FormRequest)
* Business Logic:

  * Tính tổng tiền
  * Trừ tồn kho
  * Transaction DB
* Notification (Email)
* Logging & Error handling
* Pagination & tối ưu hiệu năng

---

### 🔥 Nâng cao

* Thanh toán online (VNPay / Momo)
* AI Chat tư vấn
* Recommendation system
* Wishlist
* Coupon / Discount
* SEO (slug, sitemap, meta)

---

## 🗂️ Cấu trúc dự án

```
app/
 ├── Models/
 ├── Http/
 │    ├── Controllers/
 │    ├── Requests/
 │    └── Middleware/
 ├── Services/
 ├── Repositories/

resources/views/
routes/
database/
docker/
```

---

## 🗄️ Database (tóm tắt)

| Bảng          | Mô tả        |
| ------------- | ------------ |
| users         | Người dùng   |
| products      | Sản phẩm     |
| categories    | Danh mục     |
| orders        | Đơn hàng     |
| order_details | Chi tiết đơn |
| reviews       | Đánh giá     |
| coupons       | Mã giảm giá  |

---

## ⚡ Cài đặt (Local)

```bash
git clone <repo-url>
cd project

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan serve
```

---

## 🐳 Docker Setup

### 1. Build & chạy container

Trước khi chạy Docker, tạo file môi trường:

```bash
cp .env.example .env
```

Sau đó chỉnh một số biến quan trọng trong `.env` cho Docker:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=BTL_CD1
DB_USERNAME=laravel
DB_PASSWORD=laravel

MYSQL_DATABASE=BTL_CD1
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_USER=laravel
MYSQL_PASSWORD=your_app_password
PMA_HOST=db
PMA_PORT=3306
PMA_USER=root
PMA_PASSWORD=your_root_password
```

```bash
docker-compose up -d --build
```

---

### 2. Truy cập hệ thống

```
http://localhost:8000
```

---

### 3. Chạy lệnh Laravel trong container

```bash
docker exec -it laravel_app bash

php artisan migrate
php artisan db:seed
```

---

### 4. Lưu ý bảo mật biến môi trường

- Không commit file `.env` lên GitHub
- Chỉ commit file mẫu `.env.example`
- API keys/secret keys phải cấu hình qua biến môi trường trong `.env`

---

## 📏 Quy ước phát triển

* Controller chỉ xử lý request
* Business logic đặt trong Service
* Validate bằng FormRequest
* Sử dụng Eloquent ORM
* Tuân thủ chuẩn PSR-12

---

## 👥 Thành viên

* Trưởng nhóm: ...
* Thành viên: ...

---

## 📌 Ghi chú

* Không commit `.env`
* Sử dụng `.gitignore` chuẩn Laravel
* Làm việc theo Git flow

---

## 📄 License

Dự án sử dụng framework Laravel (MIT License).
