# CHECKLIST KIỂM THỬ TRƯỚC VÀ SAU DEPLOYMENT (PRODUCTION DEPLOYMENT CHECKLIST)
## Dự án: Makeup Artist Website

---

## 1. Checklist Trước Khi Triển Khai (Pre-Deployment Checklist)

- [ ] **Môi trường & Config**:
  - [ ] `APP_ENV=production` trong file `.env`.
  - [ ] `APP_DEBUG=false` trong file `.env`.
  - [ ] `APP_KEY` đã được tạo và không để trống.
  - [ ] `APP_URL` đúng định dạng domain thực tế kèm `https://`.
- [ ] **Bảo mật**:
  - [ ] Mật khẩu Database mạnh (ít nhất 16 ký tự, gồm số, chữ hoa, ký tự đặc biệt).
  - [ ] Đổi mật khẩu tài khoản Admin mặc định (`trantu143444@gmail.com`).
  - [ ] File `.env` không nằm trong danh sách public hoặc cho phép tải trực tiếp qua Nginx (`location ~ /\.(?!well-known).* { deny all; }`).
  - [ ] Đã cấp quyền `775` cho `storage` và `bootstrap/cache`, các thư mục khác để `755`, file để `644`.
- [ ] **Tài nguyên & Async**:
  - [ ] Đã cấu hình `TINYPNG_API_KEY` trong `.env`.
  - [ ] Đã cấu hình SMTP Mail (`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PASSWORD`,...).
  - [ ] Supervisor đã được cấu hình và chạy tiến trình `queue:work`.
- [ ] **Build Assets**:
  - [ ] Đã chạy `npm run build` và file manifest/css/js tồn tại trong `public/build/`.
  - [ ] Đã chạy `php artisan storage:link` để tạo symlink `public/storage` -> `storage/app/public`.

---

## 2. Checklist Khi Chạy Deployment (Execution Checklist)

- [ ] Chạy `php artisan migrate --force` thành công không có lỗi.
- [ ] Chạy `php artisan config:cache` thành công.
- [ ] Chạy `php artisan route:cache` thành công.
- [ ] Chạy `php artisan view:cache` thành công.
- [ ] Chạy `php artisan queue:restart` để worker tải code mới.
- [ ] Khởi động lại hoặc reload `php-fpm` và `nginx`.

---

## 3. Checklist Kiểm Thử Sau Triển Khai (Post-Deployment Smoke Tests)

### A. Giao diện Người dùng (Public Web)
- [ ] Truy cập `https://yourdomain.com/` (Trang chủ tải mượt mà, layout chuẩn Vite CSS).
- [ ] Truy cập `https://yourdomain.com/services` (Hiển thị đầy đủ menu bảng giá dịch vụ).
- [ ] Truy cập `https://yourdomain.com/services/1` (Chi tiết dịch vụ hiển thị chuẩn, không lỗi 404).
- [ ] Truy cập `https://yourdomain.com/portfolio` (Bộ sưu tập ảnh tải nhanh).
- [ ] Truy cập `https://yourdomain.com/posts` & `https://yourdomain.com/posts/{slug}` (Bài viết tải tốt).
- [ ] Thử submit form Đặt lịch tại `https://yourdomain.com/booking`:
  - [ ] Submit hợp lệ -> Thông báo thành công hiển thị, bản ghi tạo trong DB.
  - [ ] Thử spam submit liên tục -> Rate limiter chặn sau 10 requests.

### B. Quản trị Hệ thống (Admin Panel)
- [ ] Truy cập `https://yourdomain.com/admin` (Trang đăng nhập Filament hiển thị).
- [ ] Đăng nhập tài khoản Admin -> Dashboard hiển thị chính xác các chỉ số thống kê.
- [ ] Upload ảnh Banner / Post / Portfolio mới:
  - [ ] Upload thành công ngay lập tức không bị treo trang (do TinyPNG đã chuyển sang Queue Job).
  - [ ] Queue Worker xử lý nén ảnh thành công trong background log (`storage/logs/worker.log`).
- [ ] Truy cập hóa đơn `GET /booking/{id}/invoice`:
  - [ ] Khi chưa đăng nhập -> Chặn và chuyển hướng về trang login admin.
  - [ ] Khi đã đăng nhập -> Hiển thị trang in hóa đơn chuẩn.

### C. Giám sát & Logs (Monitoring)
- [ ] Kiểm tra file `storage/logs/laravel.log` không có exception hay fatal error.
- [ ] Kiểm tra Supervisor: `sudo supervisorctl status` báo `RUNNING`.
- [ ] Kiểm tra Nginx access & error logs: `sudo tail -f /var/log/nginx/error.log`.
