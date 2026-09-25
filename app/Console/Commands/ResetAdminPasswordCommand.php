<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email? : Email của tài khoản Admin cần đổi mật khẩu} {--password= : Mật khẩu mới}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đặt lại mật khẩu cho tài khoản Quản trị viên (Admin / Super Admin) một cách nhanh chóng';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Nhập địa chỉ Email của tài khoản Admin cần đặt lại mật khẩu:');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Không tìm thấy tài khoản nào có email: [{$email}]");
            return self::FAILURE;
        }

        $newPassword = $this->option('password');
        if (!$newPassword) {
            $newPassword = $this->secret('Nhập mật khẩu mới cho tài khoản (sẽ được ẩn khi gõ):');
            if (empty($newPassword)) {
                $this->error('❌ Mật khẩu không được để trống!');
                return self::FAILURE;
            }
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $this->info("✓ Đặt lại mật khẩu thành công cho tài khoản [{$user->name} ({$user->email})]!");
        $this->line("👉 Bạn có thể đăng nhập ngay tại: " . url('/admin/login'));

        return self::SUCCESS;
    }
}
