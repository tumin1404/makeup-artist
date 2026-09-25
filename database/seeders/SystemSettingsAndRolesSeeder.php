<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemSettingsAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------------------------------------
        // 1. NẠP ĐẦY ĐỦ 108 MỤC CẤU HÌNH HỆ THỐNG TRÊN TẤT CẢ 15 NHÓM
        // -----------------------------------------------------------------------------
        
        // Đọc cấu hình chuẩn từ DatabaseSeeder
        $databaseSeederContent = file_get_contents(database_path('seeders/DatabaseSeeder.php'));
        
        // Trích xuất mảng $standardSettings bằng reflection hoặc regex an toàn
        preg_match('/\$standardSettings\s*=\s*(\[.*?\]);\s*foreach/s', $databaseSeederContent, $matches);
        
        if (!empty($matches[1])) {
            eval('$standardSettings = ' . $matches[1] . ';');
            
            foreach ($standardSettings as $setting) {
                $existing = Setting::where('key', $setting['key'])->first();
                
                // Nếu đã có giá trị khác rỗng thì giữ nguyên giá trị người dùng đã nhập
                $valueToSave = ($existing && filled($existing->value)) 
                    ? $existing->value 
                    : $setting['value'];

                Setting::updateOrCreate(
                    ['key' => $setting['key']],
                    [
                        'group' => $setting['group'],
                        'description' => $setting['description'],
                        'recommendation' => $setting['recommendation'] ?? null,
                        'value' => $valueToSave,
                        'type' => $setting['type'],
                    ]
                );
            }
        }

        // Đảm bảo các cấu hình liên hệ, MXH và quy trình chuẩn xác
        $essentialSettings = [
            'process_step_3_desc' => 'Đến đúng giờ, chuẩn bị mỹ phẩm high-end chính hãng, bộ cọ vệ sinh sạch khuẩn và thực hiện makeup chuyên nghiệp tôn vinh vẻ đẹp riêng.',
            'social_facebook' => 'https://facebook.com/thaomakeup.luxury',
            'social_instagram' => 'https://instagram.com/thaomakeup_artist',
            'social_tiktok' => 'https://tiktok.com/@thaomakeup.vn',
            'social_zalo' => 'https://zalo.me/0912345678',
            'social_youtube' => 'https://youtube.com/@thaomakeupartist',
            'hotline' => '0912.345.678',
            'email' => 'thaomakeup.studio@gmail.com',
            'address_main' => 'Tầng 3, Toà Luxury Building, 102 Vũ Phạm Hàm, Cầu Giấy, Hà Nội',
            'address_branch' => 'Khu Đô Thị Ecopark, Văn Giang, Hưng Yên',
            'address' => 'Hà Nội & Các tỉnh thành lân cận (Nhận lưu động tận nơi)',
            'working_hours' => '07:30 - 21:00 (Tất cả các ngày trong tuần)',
            'map_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.113264426544!2d105.7951234!3d21.0281328!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4cd0c66f05%3A0x628045610850c95!2zMTAyIFbFqSBQaOG6oW0gSMOgbSwgWcOqbiBIw7JhLCBD4bqndSBHaeG6pXksIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2svn!4v1710000000000!5m2!1svi!2svn',
        ];

        foreach ($essentialSettings as $k => $v) {
            Setting::where('key', $k)->update(['value' => $v]);
        }

        Cache::forget('site_settings');

        // -----------------------------------------------------------------------------
        // 2. CẬP NHẬT CẤP QUYỀN VAI TRÒ (ROLES & PERMISSIONS)
        // -----------------------------------------------------------------------------
        
        // Reset cache permission của Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Danh sách tất cả quyền trong hệ thống
        $allPermissions = Permission::all();

        // 1. Role Super Admin: Full quyền
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);

        // 2. Role Quản Lý Studio (Manager): Quản lý Đặt lịch, Thu chi, Dịch vụ, Bài viết, Bộ sưu tập, Cài đặt
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', '%booking%')
                  ->orWhere('name', 'like', '%payment%')
                  ->orWhere('name', 'like', '%expense%')
                  ->orWhere('name', 'like', '%service%')
                  ->orWhere('name', 'like', '%post%')
                  ->orWhere('name', 'like', '%portfolio%')
                  ->orWhere('name', 'like', '%banner%')
                  ->orWhere('name', 'like', '%category%')
                  ->orWhere('name', 'like', '%setting%')
                  ->orWhere('name', 'like', '%widget%')
                  ->orWhere('name', 'like', '%page%');
        })->get();
        $managerRole->syncPermissions($managerPermissions);

        // 3. Role Nhân Viên Makeup (Staff): Xem/cập nhật Đặt lịch, Xem dịch vụ, Thêm ảnh bộ sưu tập
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffPermissions = Permission::where(function ($query) {
            $query->whereIn('name', [
                'view_booking', 'view_any_booking', 'update_booking',
                'view_service', 'view_any_service',
                'view_portfolio', 'view_any_portfolio', 'create_portfolio',
                'view_post', 'view_any_post',
                'view_DashboardStats', 'view_FinancialChart', 'view_LatestBookingsTable',
            ]);
        })->get();
        $staffRole->syncPermissions($staffPermissions);

        // -----------------------------------------------------------------------------
        // 3. GÁN ROLE SUPER ADMIN CHO TÀI KHOẢN ADMIN
        // -----------------------------------------------------------------------------
        $adminUser = User::where('email', 'trantu143444@gmail.com')->first();
        if ($adminUser) {
            if (!$adminUser->hasRole('super_admin')) {
                $adminUser->assignRole('super_admin');
            }
        }
    }
}
