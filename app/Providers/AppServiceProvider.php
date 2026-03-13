<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kéo toàn bộ dữ liệu setting và chia sẻ cho tất cả các file view
        if (Schema::hasTable('settings')) {
            $settings = Setting::pluck('value', 'key')->toArray();
            View::share('settings', $settings);
        }
    }
}
