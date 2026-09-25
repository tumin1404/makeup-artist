# SERVER REQUIREMENTS & INFRASTRUCTURE SPECIFICATION
## Dự án: Makeup Artist Website (Laravel 12 + Filament v3 + MySQL)

Tài liệu này quy định cấu hình hạ tầng tối thiểu và khuyến nghị để vận hành hệ thống Makeup Artist website 24/7 ổn định, an toàn và chịu tải tốt.

---

## 1. Cấu hình Máy chủ (VPS / Dedicated Server)

### A. Cấu hình Tối thiểu (Minimum - 100-500 visits/ngày)
- **CPU**: 2 vCPU
- **RAM**: 2 GB (khuyến nghị kèm 2GB Swap)
- **Ổ cứng**: 30 GB SSD / NVMe
- **Hệ điều hành**: Ubuntu 22.04 LTS hoặc Ubuntu 24.04 LTS (hoặc Debian 12)
- **Băng thông**: 1 Gbps port

### B. Cấu hình Khuyến nghị (Recommended - 1.000 - 10.000 visits/ngày)
- **CPU**: 4 vCPU
- **RAM**: 4 GB - 8 GB
- **Ổ cứng**: 50 - 100 GB NVMe SSD
- **Hệ điều hành**: Ubuntu 24.04 LTS
- **Backup**: Daily automated snapshots

---

## 2. Phần mềm & Runtime Yêu cầu

### A. PHP Runtime
- **PHP Version**: 8.2+ (Đã tương thích tốt với PHP 8.3 / 8.4 / 8.5)
- **PHP Extensions bắt buộc**:
  - `php-fpm` (FastCGI Process Manager)
  - `php-mysql` / `php-pdo` (MySQL PDO Driver)
  - `php-mbstring`
  - `php-xml` / `php-dom` / `php-simplexml`
  - `php-curl`
  - `php-zip`
  - `php-bcmath`
  - `php-intl` (Cần thiết cho Filament / Localization)
  - `php-fileinfo`
  - `php-gd` hoặc `php-imagick` (Xử lý ảnh đại diện & upload)
  - `php-sqlite3` (Chạy automated test suite)
  - `php-redis` (Khuyến nghị cho Cache & Queue)

### B. Cơ sở dữ liệu (Database Server)
- **MySQL**: 8.0+ hoặc **MariaDB**: 10.6+ / 11.0+
- **Character Set**: `utf8mb4`
- **Collation**: `utf8mb4_unicode_ci`
- **SQL Mode**: Standard Laravel default (strict mode an toàn)

### C. Web Server & Reverse Proxy
- **Nginx**: 1.22+ hoặc 1.24+ (Khuyến nghị)
- **Caddy**: 2.7+ (Hỗ trợ Auto SSL Let's Encrypt tiện lợi)
- **HTTPS**: Bắt buộc chứng chỉ SSL/TLS (Let's Encrypt / Cloudflare SSL)
- **HTTP/2**: Đã bật

### D. Node.js (Build tools)
- **Node.js**: 20 LTS hoặc 22 LTS (dùng trong quy trình CI/CD build Vite assets)
- **NPM**: 10+

### E. Queue & Cache Storage (Khuyến nghị)
- **Redis Server**: 6.x hoặc 7.x (Dùng cho Redis Cache, Redis Queue Worker, Redis Session)
- Hoặc Database Queue (`queue:table` / `jobs` table) kèm Supervisor.

---

## 3. Cấu hình PHP-FPM & Nginx tối ưu

### A. `php.ini` Settings
```ini
max_execution_time = 60
max_input_time = 60
memory_limit = 256M
post_max_size = 25M
upload_max_filesize = 20M
expose_php = Off
display_errors = Off
opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 0
opcache.validate_timestamps = 0 ; Bật =0 cho production (nhớ reload php-fpm khi deploy)
```

### B. Nginx Security Headers
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;
```

---

## 4. Dịch vụ Nền & Quản lý Tiến trình (Process Supervisors)

1. **Systemd / Supervisor**:
   - `laravel-worker` (Quản lý tiến trình `php artisan queue:work --tries=3 --timeout=90`)
   - `cron` (Chạy `php artisan schedule:run` mỗi phút)
2. **UFW Firewall**:
   - Chỉ mở port 22 (SSH), 80 (HTTP), 443 (HTTPS).
   - Đóng toàn bộ port 3306 (MySQL), 6379 (Redis) từ internet bên ngoài.
