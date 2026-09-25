#!/usr/bin/env bash
set -e

echo "🚀 [1/6] Bắt đầu quy trình triển khai ứng dụng Makeup Artist..."

# 1. Kéo code mới nhất
git pull origin main

# 2. Cài đặt dependencies PHP (production)
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Cài đặt dependencies Node.js & Build Production Assets
npm ci
npm run build

# 4. Chạy Migration cơ sở dữ liệu
php artisan migrate --force

# 5. Tối ưu hóa và Cache cấu hình Laravel
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Tạo Symlink Storage (nếu chưa có) và Phân quyền
php artisan storage:link || true
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 7. Khởi động lại Queue Worker qua Supervisor & Reload PHP-FPM
php artisan queue:restart
if command -v supervisorctl >/dev/null 2>&1; then
    sudo supervisorctl restart all || true
fi

if systemctl is-active --quiet php8.3-fpm; then
    sudo systemctl reload php8.3-fpm
elif systemctl is-active --quiet php8.4-fpm; then
    sudo systemctl reload php8.4-fpm
fi

echo "✅ [6/6] Triển khai thành công! Ứng dụng đã sẵn sàng phục vụ."
