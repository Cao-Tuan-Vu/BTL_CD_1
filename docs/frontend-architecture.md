# Frontend Architecture (Blade)

## Muc tieu
- Tach layout theo domain (customer/admin).
- Tach CSS theo tang: global -> layout -> page.
- Blade chi render HTML + logic hien thi du lieu.
- JS theo tung page/feature, khong nhung script inline.

## Cau truc thu muc
- `resources/views/customer/layouts/app.blade.php`: shell customer.
- `resources/views/customer/layouts/partials/*`: header, footer, chat widget.
- `resources/views/admin/layouts/app.blade.php`: shell admin.
- `resources/views/admin/layouts/partials/*`: sidebar va block dung lai.
- `resources/views/admin/layouts/auth.blade.php`: shell cho auth admin.
- `public/css/global.css`: reset + utility chung.
- `public/css/customer/layout.css`: style khung customer.
- `public/css/customer/pages/*.css`: style rieng tung trang customer.
- `public/css/admin/layout.css`: style khung admin.
- `public/css/admin/auth.css`: style auth admin.
- `public/js/customer/*.js`: script page-level customer.

## Quy uoc them trang moi
1. View page khong viet inline `<style>`/`<script>`.
2. Tao file CSS page trong `public/css/{area}/pages/` va nap qua `@push('styles')`.
3. Tao file JS page trong `public/js/{area}/` va nap qua `@push('scripts')`.
4. Component dung lai dat trong `resources/views/{area}/layouts/partials`.

## Chuan hoa phase 2
- Khong su dung thuoc tinh `style="..."` trong Blade customer/admin.
- Blade chi giu cau truc HTML + render data + include/push assets.
- CSS page-level su dung class generated theo pattern `.pv-*` cho style migration.

## Script migrate
- Script: `scripts/migrate-inline-styles.ps1`
- Muc dich: chuyen style inline trong customer/admin sang CSS page-level va tu chen link CSS.
- Cach chay:

```powershell
Set-Location "d:\Laravel CD1\BTL_chuyen_de_1"
.\scripts\migrate-inline-styles.ps1
```

## Nguyen tac dependency
- `global.css` khong duoc phu thuoc layout.
- `layout.css` co the su dung utility trong `global.css`.
- `page.css` chi override theo namespace trang.
- JS page chi thao tac trong namespace DOM cua page do.
