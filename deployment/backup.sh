#!/usr/bin/env bash
set -e

# ==============================================================================
# Script Tự Động Sao Lưu Database & Tệp Storage Cho Dự Án Makeup Artist
# Gắn vào Crontab chạy mỗi đêm lúc 02:00:
# 0 2 * * * /var/www/makeup-artist/deployment/backup.sh >> /var/log/makeup_backup.log 2>&1
# ==============================================================================

BACKUP_DIR="/var/backups/makeup-artist"
DATE=$(date +'%Y-%m-%d_%H-%M-%S')
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

mkdir -p "$BACKUP_DIR"

echo "📦 [${DATE}] Bắt đầu sao lưu hệ thống Makeup Artist..."

# 1. Nạp biến môi trường từ .env
if [ -f "$PROJECT_DIR/.env" ]; then
    export $(grep -v '^#' "$PROJECT_DIR/.env" | xargs)
fi

DB_USER="${DB_USERNAME:-root}"
DB_PASS="${DB_PASSWORD:-}"
DB_NAME="${DB_DATABASE:-makeup_artist_db}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

# 2. Dump Database và Nén Gzip
DB_BACKUP_FILE="${BACKUP_DIR}/db_${DB_NAME}_${DATE}.sql.gz"
echo "🗄️ Đang sao lưu Database: ${DB_NAME}..."

if [ -n "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" \
        --single-transaction --quick --routines --triggers "$DB_NAME" | gzip -9 > "$DB_BACKUP_FILE"
else
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" \
        --single-transaction --quick --routines --triggers "$DB_NAME" | gzip -9 > "$DB_BACKUP_FILE"
fi

echo "✅ Đã tạo file sao lưu DB: ${DB_BACKUP_FILE} ($(du -h "$DB_BACKUP_FILE" | cut -f1))"

# 3. Tự động dọn dẹp các bản sao lưu cũ hơn 14 ngày
echo "🧹 Đang dọn dẹp các bản sao lưu cũ hơn 14 ngày..."
find "$BACKUP_DIR" -type f -name "db_${DB_NAME}_*.sql.gz" -mtime +14 -exec rm -f {} \;

echo "🎉 [${DATE}] Sao lưu hoàn tất thành công!"
