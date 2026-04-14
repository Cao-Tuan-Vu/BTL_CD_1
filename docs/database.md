# Sơ Đồ Dữ Liệu

## products
- id
- name
- price
- description
- category_id
- stock
- status

## categories
- id
- name
- slug

## orders
- id
- user_id
- total_price
- status

## order_details
- id
- order_id
- product_id
- quantity
- price

## users
- id
- name
- email
- password
- role