<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use App\Services\Media\MediaOptimizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaOptimizerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_ffmpeg_is_detected_and_available(): void
    {
        $optimizer = app(MediaOptimizerService::class);
        $this->assertTrue($optimizer->isFFmpegAvailable());
        $this->assertNotNull($optimizer->getFFmpegBinaryPath());
        $this->assertFileExists($optimizer->getFFmpegBinaryPath());
    }

    public function test_service_can_optimize_image_to_webp(): void
    {
        $optimizer = app(MediaOptimizerService::class);

        // Create a test image
        $file = UploadedFile::fake()->image('test_photo.jpg', 800, 600);
        $storedPath = $file->store('portfolios', 'public');

        $this->assertTrue(Storage::disk('public')->exists($storedPath));

        $result = $optimizer->optimizeImage($storedPath, ['maxWidth' => 600, 'quality' => 80]);

        $this->assertTrue($result['success']);
        $this->assertStringEndsWith('.webp', $result['path']);
        $this->assertTrue(Storage::disk('public')->exists($result['path']));
    }

    public function test_service_handles_svg_without_distortion(): void
    {
        $optimizer = app(MediaOptimizerService::class);

        $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="50" cy="50" r="40" fill="red" /></svg>';
        Storage::disk('public')->put('settings/logo.svg', $svgContent);

        $result = $optimizer->optimizeImage('settings/logo.svg');

        $this->assertTrue($result['success']);
        $this->assertEquals('settings/logo.svg', $result['path']);
    }

    public function test_portfolio_model_auto_optimizes_image_to_webp(): void
    {
        $category = Category::create([
            'name' => 'Cô dâu',
            'slug' => 'co-dau',
            'type' => Category::TYPE_PORTFOLIO,
        ]);

        $file = UploadedFile::fake()->image('bride_shot.png', 1200, 800);
        $storedPath = $file->store('portfolios', 'public');

        $portfolio = Portfolio::create([
            'category_id' => $category->id,
            'title' => 'Cô dâu rạng ngời',
            'type' => 'image',
            'file_path' => $storedPath,
            'is_active' => true,
        ]);

        $portfolio->refresh();

        // The file path should be converted to .webp
        $this->assertStringEndsWith('.webp', $portfolio->file_path);
        $this->assertTrue(Storage::disk('public')->exists($portfolio->file_path));
    }

    public function test_banner_model_auto_optimizes_image_to_webp(): void
    {
        $file = UploadedFile::fake()->image('hero_banner.jpg', 1920, 600);
        $storedPath = $file->store('banners', 'public');

        $banner = Banner::create([
            'title' => 'Khuyến mãi mùa cưới',
            'image_path' => $storedPath,
            'position' => 'home_hero',
            'is_active' => true,
        ]);

        $banner->refresh();

        $this->assertStringEndsWith('.webp', $banner->image_path);
        $this->assertTrue(Storage::disk('public')->exists($banner->image_path));
    }

    public function test_post_model_auto_optimizes_thumbnail(): void
    {
        $category = Category::create([
            'name' => 'Bí quyết makeup',
            'slug' => 'bi-quyet-makeup',
            'type' => Category::TYPE_POST,
        ]);

        $file = UploadedFile::fake()->image('article_thumb.png', 800, 500);
        $storedPath = $file->store('posts/thumbnails', 'public');

        $post = Post::create([
            'title' => 'Xu hướng makeup 2026',
            'slug' => 'xu-huong-makeup-2026',
            'category_id' => $category->id,
            'thumbnail' => $storedPath,
            'excerpt' => 'Tóm tắt bài viết...',
            'content' => '<p>Nội dung chi tiết</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $post->refresh();

        $this->assertStringEndsWith('.webp', $post->thumbnail);
        $this->assertTrue(Storage::disk('public')->exists($post->thumbnail));
    }

    public function test_expense_and_payment_models_auto_optimize_images(): void
    {
        $file1 = UploadedFile::fake()->image('product.jpg', 500, 500);
        $path1 = $file1->store('expenses/products', 'public');

        $expense = Expense::create([
            'item_name' => 'Son Tom Ford',
            'category' => 'Mỹ phẩm',
            'amount' => 1200000,
            'expense_date' => now(),
            'product_image' => $path1,
        ]);

        $expense->refresh();
        $this->assertStringEndsWith('.webp', $expense->product_image);

        $file2 = UploadedFile::fake()->image('transfer_bill.jpg', 600, 800);
        $path2 = $file2->store('payments/proofs', 'public');

        $booking = \App\Models\Booking::create([
            'customer_name' => 'Lan Anh',
            'phone' => '0901234567',
            'booking_date' => now(),
            'total_amount' => 2000000,
            'status' => 'confirmed',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'title' => 'Cọc giữ lịch',
            'amount' => 500000,
            'payment_method' => 'Chuyển khoản',
            'payment_date' => now(),
            'proof_image' => $path2,
        ]);

        $payment->refresh();
        $this->assertStringEndsWith('.webp', $payment->proof_image);
    }

    public function test_setting_model_auto_optimizes_image_type(): void
    {
        $file = UploadedFile::fake()->image('site_logo.png', 400, 120);
        $storedPath = $file->store('settings', 'public');

        $setting = Setting::create([
            'group' => 'general',
            'key' => 'test_site_logo',
            'value' => $storedPath,
            'type' => 'image',
            'description' => 'Logo thương hiệu',
        ]);

        $setting->refresh();
        $this->assertStringEndsWith('.webp', $setting->value);
        $this->assertTrue(Storage::disk('public')->exists($setting->value));
    }

    public function test_user_model_auto_optimizes_avatar(): void
    {
        $file = UploadedFile::fake()->image('artist_avatar.jpg', 300, 300);
        $storedPath = $file->store('avatars', 'public');

        $user = User::create([
            'name' => 'Master Artist',
            'email' => 'artist@makeup.test',
            'password' => 'secret123',
            'avatar' => $storedPath,
        ]);

        $user->refresh();
        $this->assertStringEndsWith('.webp', $user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_portfolio_model_handles_video_gracefully(): void
    {
        $category = Category::create([
            'name' => 'Video Clips',
            'slug' => 'video-clips',
            'type' => Category::TYPE_PORTFOLIO,
        ]);

        $videoContent = 'FAKE_MP4_BINARY_DATA';
        Storage::disk('public')->put('portfolios/clip.mp4', $videoContent);

        $portfolio = Portfolio::create([
            'category_id' => $category->id,
            'title' => 'Video makeup cô dâu',
            'type' => 'video',
            'file_path' => 'portfolios/clip.mp4',
            'is_active' => true,
        ]);

        $portfolio->refresh();

        $this->assertEquals('portfolios/clip.mp4', $portfolio->file_path);
    }

    public function test_optimize_media_artisan_command_runs(): void
    {
        // Place a sample image in storage
        $file = UploadedFile::fake()->image('sample.jpg', 400, 400);
        $storedPath = $file->store('banners', 'public');

        $this->artisan('media:optimize', ['--type' => 'images'])
            ->assertExitCode(0);
    }

    public function test_service_can_extract_poster_or_handle_video(): void
    {
        $optimizer = app(MediaOptimizerService::class);
        $this->assertNull($optimizer->extractPoster('non_existent_video.mp4'));
    }
}
