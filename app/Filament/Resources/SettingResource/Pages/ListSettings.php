<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm cấu hình mới')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->icon('heroicon-o-squares-2x2'),
            'general' => Tab::make('Chung & SEO')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'general'))
                ->icon('heroicon-o-globe-alt'),
            'contact' => Tab::make('Liên hệ & Địa chỉ')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'contact'))
                ->icon('heroicon-o-phone'),
            'social' => Tab::make('Mạng xã hội')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'social'))
                ->icon('heroicon-o-share'),
            'home' => Tab::make('Trang chủ')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'home'))
                ->icon('heroicon-o-home'),
            'about' => Tab::make('Giới thiệu & Bio')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'about'))
                ->icon('heroicon-o-user'),
            'services' => Tab::make('Dịch vụ & Báo giá')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'services'))
                ->icon('heroicon-o-sparkles'),
            'portfolio' => Tab::make('Bộ sưu tập')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'portfolio'))
                ->icon('heroicon-o-photo'),
            'blog' => Tab::make('Tạp chí & Tác giả')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'blog'))
                ->icon('heroicon-o-newspaper'),
            'booking' => Tab::make('Đặt lịch')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'booking'))
                ->icon('heroicon-o-calendar-days'),
            'banking' => Tab::make('Thanh toán & Hóa đơn')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'banking'))
                ->icon('heroicon-o-banknotes'),
            'popup' => Tab::make('Popup thông báo')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'popup'))
                ->icon('heroicon-o-megaphone'),
        ];
    }
}
