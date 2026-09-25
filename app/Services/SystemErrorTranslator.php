<?php

namespace App\Services;

use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Http\Client\ConnectionException;

class SystemErrorTranslator
{
    /**
     * Dịch và phân loại ngoại lệ kỹ thuật sang ngôn ngữ tiếng Việt dễ hiểu cho Admin.
     *
     * @param Throwable|string $exception
     * @param string|null $context
     * @return array{title: string, body: string, icon: string, color: string}
     */
    public static function translate(Throwable|string $exception, ?string $context = null): array
    {
        $message = is_string($exception) ? $exception : $exception->getMessage();
        $class = is_object($exception) ? get_class($exception) : '';

        // 1. Lỗi gửi Email / SMTP
        if (
            $exception instanceof TransportExceptionInterface ||
            str_contains($class, 'Mailer') ||
            str_contains($class, 'Transport') ||
            str_contains(strtolower($message), 'smtp') ||
            str_contains(strtolower($message), 'failed to authenticate on smtp') ||
            str_contains(strtolower($message), 'connection could not be established with host')
        ) {
            return [
                'title' => 'Lỗi kết nối máy chủ gửi thư (Email / SMTP)',
                'body' => 'Không thể gửi email thông báo do kết nối tới máy chủ gửi thư bị từ chối hoặc sai tài khoản SMTP. Vui lòng kiểm tra lại cấu hình MAIL_HOST, MAIL_USERNAME và MAIL_PASSWORD trong cài đặt hệ thống.',
                'icon' => 'heroicon-o-envelope',
                'color' => 'danger',
            ];
        }

        // 2. Lỗi nén ảnh & tối ưu đa phương tiện (Media Optimizer)
        if (
            str_contains(strtolower($message), 'mediaoptimizer') ||
            str_contains(strtolower($message), 'ffmpeg') ||
            str_contains(strtolower($message), 'intervention') ||
            str_contains(strtolower($message), 'tinypng') ||
            str_contains(strtolower($message), 'tinify') ||
            str_contains(strtolower($message), 'compression') ||
            str_contains($class, 'Tinify')
        ) {
            return [
                'title' => 'Lỗi dịch vụ tối ưu đa phương tiện (Media Optimizer)',
                'body' => 'Không thể hoàn tất tự động nén tối ưu hóa hình ảnh hoặc chuyển mã video. File gốc vẫn được lưu an toàn trong hệ thống.',
                'icon' => 'heroicon-o-photo',
                'color' => 'warning',
            ];
        }

        // 3. Lỗi Cơ sở dữ liệu (Database / PDO / Query)
        if (
            $exception instanceof QueryException ||
            str_contains($class, 'PDOException') ||
            str_contains(strtolower($message), 'sqlstate') ||
            str_contains(strtolower($message), 'database') ||
            str_contains(strtolower($message), 'connection refused')
        ) {
            return [
                'title' => 'Lỗi cơ sở dữ liệu (Database)',
                'body' => 'Hệ thống gặp sự cố khi đọc/ghi dữ liệu vào cơ sở dữ liệu MySQL. Vui lòng kiểm tra trạng thái hoạt động của máy chủ database hoặc đường truyền kết nối.',
                'icon' => 'heroicon-o-circle-stack',
                'color' => 'danger',
            ];
        }

        // 4. Lỗi Queue / Tác vụ nền thử lại quá số lần
        if (
            $exception instanceof MaxAttemptsExceededException ||
            str_contains(strtolower($message), 'has been attempted') ||
            str_contains(strtolower($message), 'max attempts')
        ) {
            return [
                'title' => 'Tác vụ xử lý nền vượt quá số lần thử lại',
                'body' => 'Một tác vụ nền (Queue Job' . ($context ? " trong [{$context}]" : '') . ') đã tự động thử lại nhiều lần nhưng không thành công và đã được đưa vào danh sách chờ xử lý lại.',
                'icon' => 'heroicon-o-arrow-path',
                'color' => 'warning',
            ];
        }

        // 5. Lỗi kết nối mạng bên ngoài / API Timeout
        if (
            $exception instanceof ConnectionException ||
            str_contains(strtolower($message), 'curl error') ||
            str_contains(strtolower($message), 'timed out') ||
            str_contains(strtolower($message), 'timeout')
        ) {
            return [
                'title' => 'Lỗi kết nối mạng hoặc dịch vụ bên ngoài',
                'body' => 'Yêu cầu kết nối tới dịch vụ bên ngoài bị quá thời gian chờ (Timeout). Vui lòng kiểm tra đường truyền Internet của máy chủ.',
                'icon' => 'heroicon-o-globe-alt',
                'color' => 'warning',
            ];
        }

        // 6. Lỗi quyền ghi tập tin hoặc thư mục
        if (
            str_contains(strtolower($message), 'permission denied') ||
            str_contains(strtolower($message), 'failed to open stream') ||
            str_contains(strtolower($message), 'storage')
        ) {
            return [
                'title' => 'Lỗi phân quyền tập tin / Lưu trữ',
                'body' => 'Hệ thống không có quyền ghi tập tin vào thư mục storage hoặc thư mục tải lên. Vui lòng cấp quyền ghi cho thư mục lưu trữ (Storage Permissions).',
                'icon' => 'heroicon-o-folder-minus',
                'color' => 'danger',
            ];
        }

        // 7. Lỗi chung / Sự cố kỹ thuật hệ thống
        $shortMessage = mb_substr($message, 0, 150) . (mb_strlen($message) > 150 ? '...' : '');

        return [
            'title' => 'Sự cố hệ thống' . ($context ? " ({$context})" : ''),
            'body' => "Hệ thống phát sinh lỗi: {$shortMessage}. Đội ngũ quản trị nên kiểm tra file nhật ký (Log) để theo dõi chi tiết.",
            'icon' => 'heroicon-o-exclamation-triangle',
            'color' => 'danger',
        ];
    }
}
