<?php

namespace Tests\Feature;

use App\Filament\Pages\ThemeSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ThemeSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create([
            'email' => 'admin_theme_' . uniqid() . '@example.com',
            'email_verified_at' => now(),
        ]);
        $this->superAdmin->assignRole($role);

        Cache::forget('site_settings');
    }

    /**
     * Test admin can access the theme settings page.
     */
    public function test_admin_can_access_theme_settings_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/theme-settings');
        $response->assertStatus(200);
        $response->assertSee('Tùy biến Giao diện, Màu sắc & Phông chữ');
        $response->assertSee('Bộ Sưu Tập Giao Diện Chuẩn Ngành');
        $response->assertSee('Live Preview Studio');
    }

    /**
     * Test theme presets definition.
     */
    public function test_theme_presets_contain_all_required_presets_and_keys(): void
    {
        $presets = ThemeSettings::getPresets();

        $this->assertArrayHasKey('nude_luxury', $presets);
        $this->assertArrayHasKey('rose_gold', $presets);
        $this->assertArrayHasKey('minimalist', $presets);
        $this->assertArrayHasKey('emerald', $presets);
        $this->assertArrayHasKey('royal_velvet', $presets);

        foreach ($presets as $key => $preset) {
            $this->assertArrayHasKey('name', $preset);
            $this->assertArrayHasKey('primary', $preset);
            $this->assertArrayHasKey('gold', $preset);
            $this->assertArrayHasKey('dark', $preset);
            $this->assertArrayHasKey('button', $preset);
            $this->assertArrayHasKey('button_text', $preset);
            $this->assertArrayHasKey('heading_font', $preset);
            $this->assertArrayHasKey('body_font', $preset);
        }
    }

    /**
     * Test saving theme settings via Livewire form component.
     */
    public function test_theme_settings_can_be_updated_and_persisted(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ThemeSettings::class)
            ->fillForm([
                'theme_preset' => 'rose_gold',
                'theme_color_primary' => '#faf0f2',
                'theme_color_gold' => '#d49b9b',
                'theme_color_dark' => '#4a2533',
                'theme_color_button' => '#4a2533',
                'theme_color_button_text' => '#ffffff',
                'theme_font_heading_type' => 'google',
                'theme_font_heading' => 'Cormorant Garamond',
                'theme_font_body_type' => 'google',
                'theme_font_body' => 'Montserrat',
                'theme_custom_css' => '.test-class { color: #d49b9b; }',
            ])
            ->call('submit')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'key' => 'theme_color_primary',
            'value' => '#faf0f2',
            'group' => 'theme',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'theme_color_gold',
            'value' => '#d49b9b',
            'group' => 'theme',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'theme_font_heading',
            'value' => 'Cormorant Garamond',
            'group' => 'theme',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'theme_custom_css',
            'value' => '.test-class { color: #d49b9b; }',
            'group' => 'theme',
        ]);
    }

    /**
     * Test applying a preset fills data correctly.
     */
    public function test_applying_theme_preset_fills_form_data(): void
    {
        $this->actingAs($this->superAdmin);

        $component = Livewire::test(ThemeSettings::class)
            ->call('applyPreset', 'emerald');

        $component->assertSet('data.theme_preset', 'emerald')
            ->assertSet('data.theme_color_primary', '#f4f8f6')
            ->assertSet('data.theme_color_gold', '#c9a050')
            ->assertSet('data.theme_color_dark', '#1d3b32')
            ->assertSet('data.theme_color_button', '#1d3b32')
            ->assertSet('data.theme_font_heading', 'Lora')
            ->assertSet('data.theme_font_body', 'Be Vietnam Pro');
    }

    /**
     * Test frontend HTML renders dynamic CSS variables and Google Fonts.
     */
    public function test_frontend_renders_dynamic_theme_variables_and_fonts(): void
    {
        Setting::updateOrCreate(
            ['key' => 'theme_color_primary'],
            ['group' => 'theme', 'value' => '#f4f8f6', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_color_gold'],
            ['group' => 'theme', 'value' => '#c9a050', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_color_dark'],
            ['group' => 'theme', 'value' => '#1d3b32', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_color_button'],
            ['group' => 'theme', 'value' => '#1d3b32', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_color_button_text'],
            ['group' => 'theme', 'value' => '#ffffff', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_font_heading_type'],
            ['group' => 'theme', 'value' => 'google', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_font_heading'],
            ['group' => 'theme', 'value' => 'Lora', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_font_body_type'],
            ['group' => 'theme', 'value' => 'google', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_font_body'],
            ['group' => 'theme', 'value' => 'Be Vietnam Pro', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify CSS Variables
        $response->assertSee('--color-primary: #f4f8f6', false);
        $response->assertSee('--color-gold: #c9a050', false);
        $response->assertSee('--color-dark: #1d3b32', false);
        $response->assertSee('--color-button: #1d3b32', false);
        $response->assertSee('--font-serif: \'Lora\', serif', false);
        $response->assertSee('--font-sans: \'Be Vietnam Pro\', sans-serif', false);

        // Verify Google Fonts URL link
        $response->assertSee('fonts.googleapis.com/css2', false);
        $response->assertSee('family=Lora', false);
        $response->assertSee('family=Be+Vietnam+Pro', false);
    }

    /**
     * Test frontend HTML renders custom uploaded font-face.
     */
    public function test_frontend_renders_custom_uploaded_font_face(): void
    {
        Setting::updateOrCreate(
            ['key' => 'theme_font_heading_type'],
            ['group' => 'theme', 'value' => 'custom', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_custom_heading_font_file'],
            ['group' => 'theme', 'value' => 'fonts/custom-luxury-font.woff2', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_font_body_type'],
            ['group' => 'theme', 'value' => 'custom', 'type' => 'text']
        );
        Setting::updateOrCreate(
            ['key' => 'theme_custom_body_font_file'],
            ['group' => 'theme', 'value' => 'fonts/custom-body-font.woff2', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify @font-face rules
        $response->assertSee("@font-face", false);
        $response->assertSee("font-family: 'CustomHeadingFont'", false);
        $response->assertSee("storage/fonts/custom-luxury-font.woff2", false);
        $response->assertSee("font-family: 'CustomBodyFont'", false);
        $response->assertSee("storage/fonts/custom-body-font.woff2", false);

        // Verify CSS variables reference custom fonts
        $response->assertSee("--font-serif: 'CustomHeadingFont', serif", false);
        $response->assertSee("--font-sans: 'CustomBodyFont', sans-serif", false);
    }

    /**
     * Test frontend injects custom CSS from theme settings.
     */
    public function test_frontend_renders_custom_injected_css(): void
    {
        Setting::updateOrCreate(
            ['key' => 'theme_custom_css'],
            ['group' => 'theme', 'value' => '.custom-studio-badge { border-radius: 999px; }', 'type' => 'textarea']
        );

        Cache::forget('site_settings');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('id="theme-custom-injected-css"', false);
        $response->assertSee('.custom-studio-badge { border-radius: 999px; }', false);
    }
}
