# HƯỚNG DẪN TRIỂN KHAI PRODUCTION 24/7 (PRODUCTION DEPLOYMENT GUIDE)
## Dự án: Makeup Artist Website (Laravel 12 + Filament v3 + MySQL)

Tài liệu này cung cấp quy trình chi tiết từng bước để triển khai website lên VPS Linux chạy Nginx, PHP 8.2+ FPM, MySQL 8.0/MariaDB và quản lý Queue bằng Supervisor.

---

## 1. Chuẩn bị Môi trường Máy chủ (Server Setup)

### Bước 1.1: Cập nhật hệ điều hành và cài đặt các gói cần thiết
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip ufw supervisor nginx
```

### Bước 1.2: Cài đặt PHP và Extensions
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd php8.3-redis \
    php8.3-sqlite3 php8.3-cli
```

### Bước 1.3: Cài đặt Composer và Node.js
```bash
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Bước 1.4: Cài đặt MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```
Tạo Database và User cho Laravel:
```sql
sudo mysql -u root -p
CREATE DATABASE makeup_artist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'makeup_user'@'localhost' IDENTIFIED BY 'MatKhauSieuManh@2026';
GRANT ALL PRIVILEGES ON makeup_artist.* TO 'makeup_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 2. Triển khai Mã nguồn (Code Deployment)

### Bước 2.1: Clone repository vào `/var/www/makeup-artist`
```bash
sudo mkdir -p /var/www/makeup-artist
sudo chown -R $USER:www-data /var/www/makeup-artist
git clone <URL_GIT_CUA_BAN> /var/www/makeup-artist
cd /var/www/makeup-artist
```

### Bước 2.2: Cấu hình file `.env`
```bash
cp .env.example .env
nano .env
```
Cấu hình các tham số quan trọng:
```ini
APP_NAME="Thảo Makeup Artist"
APP_ENV=production
APP_KEY= # Sẽ tạo ở bước sau
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=makeup_artist
DB_USERNAME=makeup_user
DB_PASSWORD=MatKhauSieuManh@2026

QUEUE_CONNECTION=database # Hoặc redis
CACHE_STORE=file # Hoặc redis
SESSION_DRIVER=file # Hoặc redis

TINYPNG_API_KEY=your_tinypng_api_key_here
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com # Hoặc Sendgrid / Postmark
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Bước 2.3: Cài đặt Dependencies & Generate Key
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
```

### Bước 2.4: Build Frontend Assets (Vite)
```bash
npm ci
npm run build
```

### Bước 2.5: Chạy Migration & Seed Ban đầu
```bash
php artisan migrate --force
# Nếu là lần đầu tiên thiết lập:
php artisan db:seed --force
```

### Bước 2.6: Tối ưu hóa Cache của Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Bước 2.7: Phân quyền thư mục an toàn
```bash
sudo chown -R www-data:www-data /var/www/makeup-artist/storage
sudo chown -R www-data:www-data /var/www/makeup-artist/bootstrap/cache
sudo chmod -R 775 /var/www/makeup-artist/storage
sudo chmod -R 775 /var/www/makeup-artist/bootstrap/cache
```

---

## 3. Cấu hình Nginx & SSL

### Bước 3.1: File Virtual Host `/etc/nginx/sites-available/makeup-artist`
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/makeup-artist/public;

    # SSL Certificates (Sau khi chạy Certbot)
    # ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php index.html;
    charset utf-8;

    # Giới hạn kích thước upload ảnh/video
    client_max_body_size 25M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|svg|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Kích hoạt site:
```bash
sudo ln -s /etc/nginx/sites-available/makeup-artist /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Bước 3.2: Cài đặt Let's Encrypt SSL tự động
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## 4. Quản lý Background Queue Worker với Supervisor

Tạo file `/etc/supervisor/conf.d/makeup-worker.conf`:
```ini
[program:makeup-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/makeup-artist/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/makeup-artist/storage/logs/worker.log
stopwaitsecs=3600
```

Kích hoạt Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start makeup-worker:*
```

---

## 5. Cấu hình Cronjob cho Laravel Scheduler

Mở Crontab của `www-data`:
```bash
sudo crontab -u www-data -e
```
Thêm dòng sau vào cuối:
```cron
* * * * * cd /var/www/makeup-artist && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Kịch bản Deploy Tự Động (Zero-Downtime Script)

Tạo file `deploy.sh` trong thư mục gốc:
```bash
#!/bin/bash
set -e

echo "🚀 Bắt đầu quá trình Deploy..."

# 1. Bật chế độ bảo trì tạm thời
php artisan down --refresh=15 --secret="bypass-deploy-token-2026"

# 2. Kéo code mới nhất từ Git
git pull origin main

# 3. Cài đặt các gói Composer mới
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Chạy Migration database an toàn
php artisan migrate --force

# 5. Build lại Frontend Assets
npm ci
npm run build

# 6. Xóa và cập nhật lại toàn bộ Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Khởi động lại Queue Worker
php artisan queue:restart
sudo supervisorctl restart makeup-worker:*

# 8. Tắt chế độ bảo trì
php artisan up

echo "✅ Deploy thành công!"
```
Cấp quyền thực thi:
```bash
chmod +x deploy.sh
```
