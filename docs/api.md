# Tài Liệu API

Đường dẫn gốc: /api

## Sản Phẩm
GET /products
GET /products/{product}
POST /products
PUT/PATCH /products/{product}
DELETE /products/{product}

Tham số lọc:
- category_id
- min_price
- max_price
- status
- q
- per_page

## Danh Mục
GET /categories
GET /categories/{category}
POST /categories
PUT/PATCH /categories/{category}
DELETE /categories/{category}

## Đơn Hàng
GET /orders
GET /orders/{order}
POST /orders
PATCH /orders/{order}/status (chỉ admin)
DELETE /orders/{order}

Dữ liệu tạo đơn hàng:
- shipping_phone
- shipping_address
- items: [{ product_id, quantity }]

Lưu ý tạo đơn hàng:
- chỉ tài khoản customer được phép tạo đơn
- hệ thống tự lấy user_id từ tài khoản đang đăng nhập

Quy tắc nghiệp vụ đơn hàng:
- kiểm tra tồn kho trước khi tạo đơn
- sử dụng DB transaction khi tạo đơn
- tính total_price từ order_details

## Đánh Giá
GET /reviews
GET /reviews/{review}
POST /reviews
PUT/PATCH /reviews/{review}
DELETE /reviews/{review}

## Validation Và Phản Hồi
- Validation sử dụng FormRequest
- API response sử dụng JsonResource
- Các endpoint danh sách trả về JSON có phân trang

## Nhánh Đơn Hàng
POST /orders
GET /orders

## Xác Thực
POST /login
POST /register