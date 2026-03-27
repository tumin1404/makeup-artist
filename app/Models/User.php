<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

    protected static function booted()
    {
        // Gửi mail verify khi Admin tạo user mới
        static::created(function ($user) {
            if (is_null($user->email_verified_at)) {
                $user->sendEmailVerificationNotification();
            }
        });

        // Tự động nén ảnh avatar khi có ảnh mới
        static::saved(function ($model) {
            $columnName = 'avatar';

            if (($model->wasRecentlyCreated || $model->wasChanged($columnName)) && !empty($model->{$columnName})) {
                $path = Storage::disk('public')->path($model->{$columnName});

                if (file_exists($path) && preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {
                    try {
                        set_time_limit(120);
                        \Tinify\setKey(env('TINYPNG_API_KEY'));
                        \Tinify\fromFile($path)->toFile($path);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Lỗi TinyPNG Avatar: ' . $e->getMessage());
                    }
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

