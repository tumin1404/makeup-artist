<?php

namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, HasAvatar, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasOptimizedMedia;

    /**
     * Media columns to automatically optimize.
     */
    public function getMediaOptimizationAttributes(): array
    {
        return [
            'avatar' => ['maxWidth' => 500, 'quality' => 80],
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        return $this->hasRole('super_admin')
            || $this->hasRole('panel_user')
            || $this->hasRole('admin')
            || $this->can('access_admin_panel');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'avatar',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    /**
     * Gửi email đặt lại mật khẩu theo mẫu cấu hình trong Notification Settings.
     */
    public function sendPasswordResetNotification($token): void
    {
        try {
            $resetUrl = url(route('filament.admin.auth.password-reset.reset', [
                'token' => $token,
                'email' => $this->getEmailForPasswordReset(),
            ], false));

            \App\Services\NotificationDispatcherService::sendPasswordReset($this, $resetUrl);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi khi gửi password reset qua NotificationDispatcherService: ' . $e->getMessage());
            parent::sendPasswordResetNotification($token);
        }
    }

    protected static function booted()
    {
        // Gửi mail verify khi Admin tạo user mới
        static::created(function ($user) {
            if (is_null($user->email_verified_at)) {
                try {
                    $user->sendEmailVerificationNotification();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Email verification notification could not be sent: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

