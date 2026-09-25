<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*'); // Tin tưởng tất cả proxy để nhận diện HTTPS
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            // Bỏ qua các ngoại lệ người dùng thông thường (404, 403, validation, redirect)
            if (
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ||
                $e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
                $e instanceof \Illuminate\Validation\ValidationException
            ) {
                return;
            }

            // Dịch và thông báo sự cố kỹ thuật đến Admin
            \App\Services\SystemNotificationService::notifySystemError($e, 'Máy chủ Web');
        });
    })->create();
