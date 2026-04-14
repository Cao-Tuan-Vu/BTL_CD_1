# BTL Chuyen De 1 - Laravel E-commerce Noi That

He thong web thuong mai dien tu noi that xay dung bang Laravel, gom 2 phan he Customer va Admin, dong thoi cung cap REST API cho cac nghiep vu chinh.

## Mo ta ngan gon

- Customer: xem san pham, gio hang, dat hang, quan ly don, danh gia.
- Admin: quan ly san pham, danh muc, don hang, nguoi dung, lien he.
- API: CRUD cho products, categories, reviews, orders.
- Tich hop chatbot tu van qua endpoint API.

## Cong nghe su dung

- PHP 8.2+
- Laravel 12
- MySQL
- Eloquent ORM
- Blade (Frontend)
- Vite + Tailwind CSS
- Composer
- Node.js + NPM
- Docker Compose (tuy chon)

## Cai dat chi tiet

### 1) Clone project

```bash
git clone <repo-url>
cd BTL_chuyen_de_1
```

### 2) Cai dat dependencies PHP

```bash
composer install
```

### 3) Cai dat dependencies frontend

```bash
npm install
```

### 4) Tao file .env


Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5) Cau hinh database trong .env

Cap nhat cac bien ket noi MySQL trong file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=BTL_CD1
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

Neu may ban dung tai khoan MySQL khac, hay doi `DB_USERNAME` va `DB_PASSWORD` cho phu hop.

### 6) Generate app key

```bash
php artisan key:generate
```

### 7) Migrate database

```bash
php artisan migrate
```

### 8) Chay server

```bash
php artisan serve
```

Mac dinh truy cap tai:

```text
http://127.0.0.1:8000
```

## Chuc nang chinh

### Customer (Web)

- Dang ky, dang nhap, quen/reset mat khau
- Xem danh sach, loc, xem chi tiet san pham
- Quan ly gio hang (them/sua/xoa)
- Checkout va dat hang (cash/momo/vnpay)
- Xem lich su don hang va chi tiet don
- Danh gia san pham
- Cap nhat thong tin va mat khau tai khoan
- Gui lien he

### Admin (Web)

- Dang nhap va dashboard tong quan
- Quan ly san pham, anh dai dien va gallery
- Quan ly danh muc
- Quan ly don hang va cap nhat trang thai
- Quan ly review
- Quan ly nguoi dung
- Xem va phan hoi lien he

### REST API

- `/api/products` (CRUD)
- `/api/categories` (CRUD)
- `/api/reviews` (CRUD)
- `/api/orders` (CRUD, update status tach rieng)
- `/api/orders/{order}/status` (admin)
- `/api/admin/overview` (admin)
- `/api/chatbot/message`

## Truy cap nhanh

- Customer: `http://127.0.0.1:8000/customer`
- Admin: `http://127.0.0.1:8000/admin/login`

## Cau truc thu muc chinh

```text
app/
  Http/
    Controllers/
      Web/
        Admin/
        Customer/
    Requests/
    Resources/
  Models/
  Repositories/
  Services/
database/
  migrations/
  seeders/
resources/
  views/
routes/
  web.php
  api.php
docs/
```

## Ghi chu

- Khong commit file `.env` len Git.
- Kien truc du an: Controller -> Service -> Repository -> Model.
