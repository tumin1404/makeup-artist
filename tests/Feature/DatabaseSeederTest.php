<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_executes_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $adminEmail = env('ADMIN_DEFAULT_EMAIL', 'admin@example.com');
        $this->assertDatabaseHas('users', [
            'email' => $adminEmail,
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'site_name',
            'group' => 'general',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'hotline',
            'group' => 'contact',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'bank_name',
            'group' => 'banking',
        ]);

        $this->assertDatabaseHas('banners', [
            'position' => 'home_hero',
        ]);

        $this->assertDatabaseHas('categories', [
            'slug' => 'meo-lam-dep',
        ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Makeup Cô Dâu (Lễ ăn hỏi)',
        ]);

        $this->assertDatabaseHas('posts', [
            'slug' => 'bi-quyet-giu-nen-1',
        ]);

        $this->assertDatabaseHas('portfolios', [
            'title' => 'Cô dâu rạng rỡ',
        ]);

        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Nguyễn Phương Ly',
        ]);
    }
}
