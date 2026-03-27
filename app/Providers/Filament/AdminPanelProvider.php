<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // THÊM 3 DÒNG NÀY (Nhớ sửa lại đường dẫn file cho đúng với thư mục public của bạn)
            ->brandLogo(function () {
                // Lưu ý: Thay 'Setting', 'key', 'value' bằng đúng tên Model và tên cột trong Database của bạn
                $logo = \App\Models\Setting::where('key', 'site_logo')->value('value');
                return $logo ? asset('storage/' . $logo) : null;
            })
            ->brandLogoHeight('3rem') // Chỉnh chiều cao logo cho vừa mắt
            ->favicon(function () {
                $favicon = \App\Models\Setting::where('key', 'site_favicon')->value('value');
                return $favicon ? asset('storage/' . $favicon) : null;
            })
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('<style>.filepond--root video { width: 100% !important; height: 100% !important; object-fit: contain !important; border-radius: 0.5rem; }</style>')
            )
            ->navigationGroups([
                NavigationGroup::make()->label('Quản lý Website'),
                NavigationGroup::make()->label('Quản lý Tài chính'),
                NavigationGroup::make()->label('Hệ thống'),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
