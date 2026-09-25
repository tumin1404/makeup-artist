<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Tự động bổ sung các HTTP Security Headers tiêu chuẩn bảo vệ website trong môi trường Production.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Chống MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Bật bộ lọc XSS của trình duyệt
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Kiểm soát thông tin Referrer khi chuyển trang
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Giới hạn quyền truy cập thiết bị phần cứng
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Giữ SAMEORIGIN để tương thích với Livewire / Filament modal & previews
        if (!$response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // Kích hoạt HSTS khi truy cập qua HTTPS hoặc cấu hình APP_URL là HTTPS
        if ($request->isSecure() || str_contains((string) config('app.url'), 'https')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Cấu hình Content-Security-Policy (CSP) tương thích với Tailwind, Livewire, Filament và các CDN
        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: https: blob:",
            "media-src 'self' data: https: blob:",
            "connect-src 'self' https: ws: wss:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self' https:",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        return $response;
    }
}
