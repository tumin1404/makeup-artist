<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\MailSettingService;
use App\Services\TelegramNotificationService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class NotificationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Cấu hình Mail & Thông báo';
    protected static ?string $title = 'Cấu hình Email, Telegram & Zalo Thông Báo';
    protected static ?string $slug = 'notification-settings';
    protected static ?int $navigationSort = 12;

    protected static string $view = 'filament.pages.notification-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('group', ['mail', 'telegram', 'zalo', 'general'])->pluck('value', 'key')->toArray();
        $siteName = $settings['site_name'] ?? 'Studio Makeup';

        $this->form->fill([
            // ----------------------------------------------------
            // MAIL SETTINGS
            // ----------------------------------------------------
            'mail_mailer' => $settings['mail_mailer'] ?? 'smtp',
            'mail_host' => $settings['mail_host'] ?? '',
            'mail_port' => $settings['mail_port'] ?? '587',
            'mail_encryption' => $settings['mail_encryption'] ?? 'tls',
            'mail_username' => $settings['mail_username'] ?? '',
            'mail_password' => $settings['mail_password'] ?? '',
            'mail_from_address' => $settings['mail_from_address'] ?? '',
            'mail_from_name' => $settings['mail_from_name'] ?? $siteName,
            'test_email_recipient' => auth()->user()?->email ?? '',

            // Mail Events & Templates
            'mail_notify_booking_enabled' => ($settings['mail_notify_booking_enabled'] ?? '1') === '1',
            'mail_notify_booking_subject' => $settings['mail_notify_booking_subject'] ?? "[{$siteName}] Xác nhận đơn đặt lịch mới từ {customer_name}",
            'mail_notify_booking_body' => $settings['mail_notify_booking_body'] ?? "Xin chào {customer_name},\n\nCảm ơn bạn đã tin tưởng và đặt lịch hẹn tại {site_name}.\nThông tin chi tiết lịch hẹn:\n- Dịch vụ: {services}\n- Ngày giờ hẹn: {booking_date}\n- Số điện thoại: {phone}\n- Tạm tính / Cọc: {total_amount}\n- Ghi chú: {notes}\n\nChúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận. Trân trọng!\n{site_name}",

            'mail_notify_login_enabled' => ($settings['mail_notify_login_enabled'] ?? '1') === '1',
            'mail_notify_login_subject' => $settings['mail_notify_login_subject'] ?? "[{$siteName}] Cảnh báo bảo mật: Đăng nhập tài khoản quản trị",
            'mail_notify_login_body' => $settings['mail_notify_login_body'] ?? "Xin chào {user_name},\n\nHệ thống ghi nhận tài khoản {user_email} vừa đăng nhập vào trang quản trị {site_name}.\n- Thời gian: {time}\n- Địa chỉ IP: {ip_address}\n- Vị trí ước tính: {location}\n- Thiết bị: {device}\n\nNếu đây là bạn, vui lòng bỏ qua thư này. Nếu không phải bạn thực hiện, hãy đổi mật khẩu quản trị ngay lập tức.",

            'mail_notify_status_enabled' => ($settings['mail_notify_status_enabled'] ?? '1') === '1',
            'mail_notify_status_subject' => $settings['mail_notify_status_subject'] ?? "[{$siteName}] Cập nhật trạng thái đơn đặt lịch #{booking_id}: {new_status}",
            'mail_notify_status_body' => $settings['mail_notify_status_body'] ?? "Xin chào {customer_name},\n\nLịch hẹn #{booking_id} của bạn tại {site_name} đã được cập nhật trạng thái mới: {new_status}.\n- Dịch vụ: {services}\n- Thời gian hẹn: {booking_date}\n- Số điện thoại: {phone}\n\nCảm ơn quý khách đã tin tưởng dịch vụ của chúng tôi!\nTrân trọng,\n{site_name}",

            'mail_notify_password_reset_enabled' => ($settings['mail_notify_password_reset_enabled'] ?? '1') === '1',
            'mail_notify_password_reset_subject' => $settings['mail_notify_password_reset_subject'] ?? "[{$siteName}] Hướng dẫn đặt lại mật khẩu tài khoản quản trị",
            'mail_notify_password_reset_body' => $settings['mail_notify_password_reset_body'] ?? "Xin chào {user_name},\n\nBạn nhận được thư này vì hệ thống ghi nhận yêu cầu đặt lại mật khẩu cho tài khoản {user_email} tại {site_name}.\nVui lòng nhấp vào đường link bên dưới để thiết lập mật khẩu mới (link có hiệu lực trong {expire_minutes} phút):\n{reset_url}\n\nNếu bạn không yêu cầu hành động này, vui lòng bỏ qua thư.",

            // ----------------------------------------------------
            // TELEGRAM SETTINGS
            // ----------------------------------------------------
            'telegram_notify_enabled' => ($settings['telegram_notify_enabled'] ?? '0') === '1',
            'telegram_bot_token' => $settings['telegram_bot_token'] ?? '',
            'telegram_chat_id' => $settings['telegram_chat_id'] ?? '',

            // Telegram Events & Templates
            'telegram_notify_booking_enabled' => ($settings['telegram_notify_booking_enabled'] ?? '1') === '1',
            'telegram_template_booking' => $settings['telegram_template_booking'] ?? "✨ <b>[{site_name}] CÓ ĐƠN ĐẶT LỊCH MỚI!</b>\n━━━━━━━━━━━━━━━━━━━\n👤 <b>Khách hàng:</b> {customer_name}\n📞 <b>Số điện thoại:</b> <a href=\"tel:{phone}\">{phone}</a>\n💄 <b>Dịch vụ:</b> {services}\n📅 <b>Thời gian hẹn:</b> {booking_date}\n💵 <b>Tạm tính / Cọc:</b> {total_amount}\n📝 <b>Ghi chú:</b> {notes}\n⏰ <b>Thời gian đặt:</b> {created_at}\n━━━━━━━━━━━━━━━━━━━\n👉 <a href=\"{admin_url}\"><b>Mở trang quản trị để xử lý đơn</b></a>",

            'telegram_notify_login_enabled' => ($settings['telegram_notify_login_enabled'] ?? '1') === '1',
            'telegram_template_login' => $settings['telegram_template_login'] ?? "🔐 <b>[{site_name}] CẢNH BÁO ĐĂNG NHẬP ADMIN!</b>\n━━━━━━━━━━━━━━━━━━━\n👤 <b>Tài khoản:</b> {user_name} ({user_email})\n🌐 <b>Địa chỉ IP:</b> <code>{ip_address}</code>\n📍 <b>Vị trí:</b> {location}\n💻 <b>Thiết bị:</b> {device}\n⏰ <b>Thời gian:</b> {time}\n━━━━━━━━━━━━━━━━━━━\n⚠️ <i>Nếu không phải bạn, hãy đổi mật khẩu admin ngay lập tức!</i>",

            'telegram_notify_status_enabled' => ($settings['telegram_notify_status_enabled'] ?? '1') === '1',
            'telegram_template_status' => $settings['telegram_template_status'] ?? "🔄 <b>[{site_name}] CẬP NHẬT TRẠNG THÁI LỊCH HẸN</b>\n━━━━━━━━━━━━━━━━━━━\n🆔 <b>Mã đơn:</b> #{booking_id}\n👤 <b>Khách hàng:</b> {customer_name} ({phone})\n📊 <b>Trạng thái mới:</b> <b>{new_status}</b>\n📅 <b>Thời gian hẹn:</b> {booking_date}\n💄 <b>Dịch vụ:</b> {services}\n━━━━━━━━━━━━━━━━━━━\n👉 <a href=\"{admin_url}\"><b>Xem chi tiết lịch hẹn</b></a>",

            // ----------------------------------------------------
            // ZALO SETTINGS
            // ----------------------------------------------------
            'zalo_notify_enabled' => ($settings['zalo_notify_enabled'] ?? '0') === '1',
            'zalo_oa_id' => $settings['zalo_oa_id'] ?? '',
            'zalo_app_id' => $settings['zalo_app_id'] ?? '',
            'zalo_secret_key' => $settings['zalo_secret_key'] ?? '',
            'zalo_template_id' => $settings['zalo_template_id'] ?? '',
            'zalo_access_token' => $settings['zalo_access_token'] ?? '',

            // Zalo Events & Templates
            'zalo_notify_booking_enabled' => ($settings['zalo_notify_booking_enabled'] ?? '0') === '1',
            'zalo_template_booking_id' => $settings['zalo_template_booking_id'] ?? '',
            'zalo_template_booking_content' => $settings['zalo_template_booking_content'] ?? 'Mẫu tin ZNS gửi khách hàng xác nhận đơn đặt lịch dịch vụ làm đẹp.',

            'zalo_notify_login_enabled' => ($settings['zalo_notify_login_enabled'] ?? '0') === '1',
            'zalo_template_login_id' => $settings['zalo_template_login_id'] ?? '',
            'zalo_template_login_content' => $settings['zalo_template_login_content'] ?? 'Mẫu tin ZNS cảnh báo phát hiện đăng nhập mới vào tài khoản quản trị.',

            'zalo_notify_status_enabled' => ($settings['zalo_notify_status_enabled'] ?? '0') === '1',
            'zalo_template_status_id' => $settings['zalo_template_status_id'] ?? '',
            'zalo_template_status_content' => $settings['zalo_template_status_content'] ?? 'Mẫu tin ZNS thông báo cập nhật trạng thái đơn hẹn.',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Cài đặt Thông báo & Email')
                    ->tabs([
                        // ==========================================
                        // TAB 1: MAIL SETTINGS & PRESETS
                        // ==========================================
                        Tabs\Tab::make('mail')
                            ->label('1. Máy Chủ Email & SMTP')
                            ->icon('heroicon-o-envelope')
                            ->badge('Domain / Gmail / Resend')
                            ->badgeColor('warning')
                            ->schema([
                                // 1.1 Presets Section (Collapsible)
                                Section::make('Mẫu Cấu Hình Nhanh 1-Click (Email Presets)')
                                    ->description('Nhấp chọn nhà cung cấp Email để tự động điền các thông số máy chủ (Host, Port, Mã hóa).')
                                    ->icon('heroicon-o-bolt')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        Actions::make([
                                            Action::make('preset_domain_ssl')
                                                ->label('🌐 Email Tên Miền (SSL 465)')
                                                ->color('warning')
                                                ->action(fn () => $this->applyPreset('domain_mail_ssl')),

                                            Action::make('preset_domain_tls')
                                                ->label('🌐 Email Tên Miền (TLS 587)')
                                                ->color('gray')
                                                ->action(fn () => $this->applyPreset('domain_mail_tls')),

                                            Action::make('preset_gmail')
                                                ->label('🔴 Gmail (App Password)')
                                                ->color('danger')
                                                ->action(fn () => $this->applyPreset('gmail')),

                                            Action::make('preset_resend')
                                                ->label('⚡ Resend (3.000 mail free/tháng)')
                                                ->color('success')
                                                ->action(fn () => $this->applyPreset('resend')),

                                            Action::make('preset_brevo')
                                                ->label('🟢 Brevo (300 mail free/ngày)')
                                                ->color('info')
                                                ->action(fn () => $this->applyPreset('brevo')),
                                        ])->columnSpanFull(),
                                    ]),

                                // 1.2 SMTP Credentials Section (Collapsible)
                                Section::make('Thông số kết nối máy chủ Email (SMTP Credentials)')
                                    ->description('Hệ thống sử dụng cấu hình này để gửi Email xác thực, Đặt lại mật khẩu, Cảnh báo đăng nhập và Hóa đơn thanh toán.')
                                    ->icon('heroicon-o-server-stack')
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Select::make('mail_mailer')
                                                ->label('Phương thức gửi Mail (Mailer)')
                                                ->options([
                                                    'smtp' => 'SMTP Server (Khuyên dùng - Chuẩn quốc tế)',
                                                    'sendmail' => 'Sendmail (Máy chủ cục bộ)',
                                                    'log' => 'Log Driver (Ghi log file thử nghiệm)',
                                                ])
                                                ->default('smtp')
                                                ->required()
                                                ->native(false),

                                            TextInput::make('mail_host')
                                                ->label('Máy chủ SMTP (Host)')
                                                ->placeholder('Ví dụ: smtp.gmail.com hoặc mail.domain.com')
                                                ->helperText('Địa chỉ máy chủ SMTP từ nhà cung cấp Mail (Domain/Gmail/Resend).')
                                                ->columnSpan(2),
                                        ]),

                                        Grid::make(3)->schema([
                                            TextInput::make('mail_port')
                                                ->label('Cổng kết nối (Port)')
                                                ->numeric()
                                                ->placeholder('587 hoặc 465')
                                                ->helperText('Thường là 587 (với TLS) hoặc 465 (với SSL).'),

                                            Select::make('mail_encryption')
                                                ->label('Giao thức mã hóa (Encryption)')
                                                ->options([
                                                    'tls' => 'TLS (Bảo mật STARTTLS - Cổng 587)',
                                                    'ssl' => 'SSL (Bảo mật SSL - Cổng 465)',
                                                    'none' => 'Không mã hóa (None - Cổng 25)',
                                                ])
                                                ->default('tls')
                                                ->native(false),

                                            TextInput::make('mail_username')
                                                ->label('Tài khoản Email đăng nhập (Username)')
                                                ->placeholder('Ví dụ: contact@yourdomain.com')
                                                ->helperText('Địa chỉ hòm thư dùng để xác thực với máy chủ SMTP.'),
                                        ]),

                                        Grid::make(3)->schema([
                                            TextInput::make('mail_password')
                                                ->label('Mật khẩu Email / App Password')
                                                ->password()
                                                ->revealable()
                                                ->placeholder('Nhập mật khẩu hoặc Mật khẩu ứng dụng 16 ký tự')
                                                ->helperText('Với Gmail: Bắt buộc dùng Mật khẩu ứng dụng (App Password).'),

                                            TextInput::make('mail_from_address')
                                                ->label('Email gửi đi hiển thị (From Address)')
                                                ->placeholder('Ví dụ: booking@yourdomain.com')
                                                ->helperText('Email hiển thị tại mục Người gửi khi khách nhận được thư.'),

                                            TextInput::make('mail_from_name')
                                                ->label('Tên người gửi hiển thị (From Name)')
                                                ->placeholder('Ví dụ: Linh Makeup Studio')
                                                ->helperText('Tên thương hiệu xuất hiện trong hộp thư khách hàng.'),
                                        ]),
                                    ]),

                                // 1.3 Email Events & Template Customization (Collapsible)
                                Section::make('Cấu Hình Sự Kiện & Mẫu Nội Dung Email Tự Động')
                                    ->description('Tùy chỉnh bật/tắt từng loại thông báo Email và chỉnh sửa mẫu tiêu đề, nội dung thư gửi đi.')
                                    ->icon('heroicon-o-document-text')
                                    ->collapsible()
                                    ->schema([
                                        Placeholder::make('mail_placeholders_guide')
                                            ->hiddenLabel()
                                            ->content(new HtmlString('
                                                <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 text-xs text-amber-900 dark:text-amber-200">
                                                    <strong>💡 Các biến động có thể sử dụng trong mẫu Email:</strong><br>
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{site_name}</code> (Tên website),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{customer_name}</code> (Tên khách),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{phone}</code> (Số điện thoại),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{services}</code> (Dịch vụ),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{booking_date}</code> (Ngày hẹn),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{total_amount}</code> (Số tiền),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{notes}</code> (Ghi chú),
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{user_name}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{ip_address}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{location}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{reset_url}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-mono">{expire_minutes}</code>
                                                </div>
                                            '))
                                            ->columnSpanFull(),

                                        // Event 1: New Booking
                                        Grid::make(1)->schema([
                                            Toggle::make('mail_notify_booking_enabled')
                                                ->label('1. Gửi Email khi có Đơn đặt lịch mới (Booking Alert)')
                                                ->helperText('Bật gửi email xác nhận đặt lịch hẹn cho khách và ban quản trị.')
                                                ->live(),

                                            TextInput::make('mail_notify_booking_subject')
                                                ->label('Tiêu đề Email Đặt Lịch')
                                                ->visible(fn (Get $get) => $get('mail_notify_booking_enabled')),

                                            Textarea::make('mail_notify_booking_body')
                                                ->label('Nội dung mẫu Email Đặt Lịch')
                                                ->rows(4)
                                                ->visible(fn (Get $get) => $get('mail_notify_booking_enabled')),
                                        ]),

                                        // Event 2: Login Alert
                                        Grid::make(1)->schema([
                                            Toggle::make('mail_notify_login_enabled')
                                                ->label('2. Gửi Email cảnh báo khi có Đăng nhập mới (Security Login Alert)')
                                                ->helperText('Bật gửi email bảo mật khi tài khoản quản trị viên đăng nhập vào hệ thống.')
                                                ->live(),

                                            TextInput::make('mail_notify_login_subject')
                                                ->label('Tiêu đề Email Cảnh Báo Đăng Nhập')
                                                ->visible(fn (Get $get) => $get('mail_notify_login_enabled')),

                                            Textarea::make('mail_notify_login_body')
                                                ->label('Nội dung mẫu Email Cảnh Báo Đăng Nhập')
                                                ->rows(4)
                                                ->visible(fn (Get $get) => $get('mail_notify_login_enabled')),
                                        ]),

                                        // Event 3: Booking Status
                                        Grid::make(1)->schema([
                                            Toggle::make('mail_notify_status_enabled')
                                                ->label('3. Gửi Email khi Cập nhật trạng thái Đơn đặt lịch (Status Changed)')
                                                ->helperText('Bật gửi email cập nhật khi đơn hẹn được duyệt, hoàn tất hoặc hủy bỏ.')
                                                ->live(),

                                            TextInput::make('mail_notify_status_subject')
                                                ->label('Tiêu đề Email Cập Nhật Trạng Thái')
                                                ->visible(fn (Get $get) => $get('mail_notify_status_enabled')),

                                            Textarea::make('mail_notify_status_body')
                                                ->label('Nội dung mẫu Email Cập Nhật Trạng Thái')
                                                ->rows(4)
                                                ->visible(fn (Get $get) => $get('mail_notify_status_enabled')),
                                        ]),

                                        // Event 4: Password Reset (Chuyên biệt Email)
                                        Grid::make(1)->schema([
                                            Toggle::make('mail_notify_password_reset_enabled')
                                                ->label('4. Gửi Email Quên Mật Khẩu / Đặt Lại Mật Khẩu (Password Reset - Chuyên biệt Email)')
                                                ->helperText('Chức năng bảo mật cốt lõi giúp quản trị viên lấy lại quyền truy cập tài khoản an toàn.')
                                                ->live(),

                                            TextInput::make('mail_notify_password_reset_subject')
                                                ->label('Tiêu đề Email Đặt Lại Mật Khẩu')
                                                ->visible(fn (Get $get) => $get('mail_notify_password_reset_enabled')),

                                            Textarea::make('mail_notify_password_reset_body')
                                                ->label('Nội dung mẫu Email Đặt Lại Mật Khẩu')
                                                ->rows(4)
                                                ->visible(fn (Get $get) => $get('mail_notify_password_reset_enabled')),
                                        ]),
                                    ]),

                                // 1.4 Test Section (Collapsible)
                                Section::make('🧪 Kiểm Tra & Gửi Thử Nghiệm Email (Test SMTP Connection)')
                                    ->description('Nhập địa chỉ email bất kỳ để gửi ngay một bức thư kiểm tra kết nối máy chủ.')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->collapsible()
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('test_email_recipient')
                                                ->label('Địa chỉ Email nhận thư kiểm tra')
                                                ->placeholder('admin@gmail.com')
                                                ->columnSpan(2),

                                            Actions::make([
                                                Action::make('send_test_email_btn')
                                                    ->label('📨 Gửi Thử Nghiệm Ngay')
                                                    ->color('warning')
                                                    ->button()
                                                    ->action(fn (Get $get) => $this->sendTestEmail($get('test_email_recipient'))),
                                            ])->columnSpan(1)->alignEnd(),
                                        ]),
                                    ]),

                                // 1.5 Detail Guide Section (Collapsible & Collapsed)
                                Section::make('📚 Hướng Dẫn Chi Tiết Cấu Hình Email & Tên Miền (Domain Mail)')
                                    ->icon('heroicon-o-book-open')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        \Filament\Forms\Components\View::make('filament.pages.partials.mail-guide')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: TELEGRAM SETTINGS
                        // ==========================================
                        Tabs\Tab::make('telegram')
                            ->label('2. Thông Báo Telegram')
                            ->icon('heroicon-o-paper-airplane')
                            ->badge('0đ Miễn Phí')
                            ->badgeColor('success')
                            ->schema([
                                // 2.1 Telegram Credentials Section (Collapsible)
                                Section::make('Cấu hình Thông Báo Telegram (Bot Riêng Cho Từng Studio)')
                                    ->description('Nhận tin nhắn báo chuông và rung tức thì trên điện thoại di động mỗi khi có sự kiện mới.')
                                    ->icon('heroicon-o-bell-alert')
                                    ->collapsible()
                                    ->schema([
                                        Toggle::make('telegram_notify_enabled')
                                            ->label('Bật hệ thống thông báo qua Telegram Bot')
                                            ->helperText('Bật công tắc chính này để kích hoạt kết nối Bot Telegram cho website.')
                                            ->columnSpanFull(),

                                        Grid::make(2)->schema([
                                            TextInput::make('telegram_bot_token')
                                                ->label('Mã Token của Bot (Telegram Bot Token)')
                                                ->password()
                                                ->revealable()
                                                ->placeholder('Ví dụ: 789123456:AAFlk..._XYZ123')
                                                ->helperText('Đoạn mã Token do @BotFather cấp khi bạn tạo Bot riêng.')
                                                ->columnSpan(1),

                                            TextInput::make('telegram_chat_id')
                                                ->label('Chat ID nhận thông báo (Telegram Chat ID)')
                                                ->placeholder('Ví dụ: 123456789 (Cá nhân) hoặc -100123456789 (Nhóm)')
                                                ->helperText('ID tài khoản của bạn hoặc ID Group chat nhận tin (lấy từ @userinfobot).')
                                                ->columnSpan(1),
                                        ]),
                                    ]),

                                // 2.2 Telegram Events & Templates (Collapsible)
                                Section::make('Cấu hình Sự Kiện & Mẫu Tin Nhắn Telegram (Hỗ trợ định dạng HTML)')
                                    ->description('Tùy chỉnh bật/tắt từng loại thông báo Telegram và cấu hình nội dung tin nhắn gửi về điện thoại.')
                                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                    ->collapsible()
                                    ->schema([
                                        Placeholder::make('telegram_placeholders_guide')
                                            ->hiddenLabel()
                                            ->content(new HtmlString('
                                                <div class="p-3.5 rounded-xl bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800/60 text-xs text-sky-900 dark:text-sky-200">
                                                    <strong>💡 Các biến động & thẻ HTML được hỗ trợ trong tin Telegram:</strong><br>
                                                    Thẻ HTML: <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">&lt;b&gt;in đậm&lt;/b&gt;</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">&lt;i&gt;in nghiêng&lt;/i&gt;</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">&lt;code&gt;đoạn mã&lt;/code&gt;</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">&lt;a href="..."&gt;đường link&lt;/a&gt;</code>.<br>
                                                    Biến: <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{site_name}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{customer_name}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{phone}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{services}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{booking_date}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{total_amount}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{admin_url}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{user_name}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{ip_address}</code>,
                                                    <code class="px-1 py-0.5 rounded bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-300 font-mono">{location}</code>
                                                </div>
                                            '))
                                            ->columnSpanFull(),

                                        // Event 1: Booking
                                        Grid::make(1)->schema([
                                            Toggle::make('telegram_notify_booking_enabled')
                                                ->label('1. Bắn tin nhắn Telegram khi có Đơn đặt lịch mới (New Booking)')
                                                ->helperText('Nhận chuông và rung lập tức khi có khách đặt lịch hẹn mới trên website.')
                                                ->live(),

                                            Textarea::make('telegram_template_booking')
                                                ->label('Mẫu tin nhắn Telegram Đơn Đặt Lịch')
                                                ->rows(5)
                                                ->visible(fn (Get $get) => $get('telegram_notify_booking_enabled')),
                                        ]),

                                        // Event 2: Login Alert
                                        Grid::make(1)->schema([
                                            Toggle::make('telegram_notify_login_enabled')
                                                ->label('2. Bắn tin nhắn Telegram khi có Đăng nhập Admin mới (Login Security Alert)')
                                                ->helperText('Cảnh báo an ninh tức thì khi có người đăng nhập vào trang quản trị.')
                                                ->live(),

                                            Textarea::make('telegram_template_login')
                                                ->label('Mẫu tin nhắn Telegram Cảnh Báo Đăng Nhập')
                                                ->rows(4)
                                                ->visible(fn (Get $get) => $get('telegram_notify_login_enabled')),
                                        ]),

                                        // Event 3: Status Changed
                                        Grid::make(1)->schema([
                                            Toggle::make('telegram_notify_status_enabled')
                                                ->label('3. Bắn tin nhắn Telegram khi Cập nhật trạng thái Lịch hẹn (Status Update)')
                                                ->helperText('Thông báo khi trạng thái đơn hẹn thay đổi sang Đã duyệt, Hoàn thành, Hủy.')
                                                ->live(),

                                            Textarea::make('telegram_template_status')
                                                ->label('Mẫu tin nhắn Telegram Cập Nhật Trạng Thái')
                                                ->rows(4)
                                                ->visible(fn (Get $get) => $get('telegram_notify_status_enabled')),
                                        ]),
                                    ]),

                                // 2.3 Test Section (Collapsible)
                                Section::make('🔔 Kiểm Tra & Bắn Tin Nhắn Thử Nghiệm Về Điện Thoại')
                                    ->description('Bấm nút bên dưới để gửi ngay 1 tin nhắn test tới Telegram của bạn để kiểm tra chuông báo, độ rung và tính chính xác của Bot Token.')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->collapsible()
                                    ->schema([
                                        Actions::make([
                                            Action::make('send_test_telegram_btn')
                                                ->label('🚀 Bắn Tin Nhắn Test Về Telegram Ngay')
                                                ->color('info')
                                                ->button()
                                                ->action(fn () => $this->sendTestTelegram()),
                                        ])->columnSpanFull(),
                                    ]),

                                // 2.4 Detail Guide Section (Collapsible & Collapsed)
                                Section::make('📖 Hướng Dẫn 3 Bước Tạo Telegram Bot Riêng Cho Studio (Chỉ 1 Phút)')
                                    ->icon('heroicon-o-academic-cap')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        \Filament\Forms\Components\View::make('filament.pages.partials.telegram-guide')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: ZALO SETTINGS
                        // ==========================================
                        Tabs\Tab::make('zalo')
                            ->label('3. Zalo ZNS / Zalo OA')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->badge('Doanh Nghiệp')
                            ->badgeColor('info')
                            ->schema([
                                // 3.1 Zalo OA Credentials (Collapsible)
                                Section::make('Cấu hình Zalo ZNS / Zalo Official Account')
                                    ->description('Tích hợp gửi thông báo qua Zalo Doanh nghiệp (Zalo OA / ZNS) dành cho các studio có đăng ký GPKD.')
                                    ->icon('heroicon-o-building-office')
                                    ->collapsible()
                                    ->schema([
                                        Toggle::make('zalo_notify_enabled')
                                            ->label('Bật tích hợp thông báo Zalo ZNS')
                                            ->helperText('Lưu ý: Zalo ZNS yêu cầu tài khoản Zalo OA Doanh nghiệp xác thực và có tính phí theo từng tin nhắn.')
                                            ->columnSpanFull(),

                                        Grid::make(2)->schema([
                                            TextInput::make('zalo_oa_id')
                                                ->label('Zalo Official Account ID (OA ID)')
                                                ->placeholder('Nhập OA ID...')
                                                ->columnSpan(1),

                                            TextInput::make('zalo_app_id')
                                                ->label('Zalo App ID (Zalo Developers)')
                                                ->placeholder('Nhập App ID...')
                                                ->columnSpan(1),

                                            TextInput::make('zalo_secret_key')
                                                ->label('Zalo Secret Key')
                                                ->password()
                                                ->revealable()
                                                ->placeholder('Nhập Secret Key...')
                                                ->columnSpan(1),

                                            TextInput::make('zalo_access_token')
                                                ->label('Zalo Access Token / Refresh Token')
                                                ->password()
                                                ->revealable()
                                                ->placeholder('Nhập Access Token...')
                                                ->columnSpan(1),
                                        ]),
                                    ]),

                                // 3.2 Zalo Events & Template IDs (Collapsible)
                                Section::make('Cấu Hình Sự Kiện & Mã Template ID Zalo ZNS')
                                    ->description('Cấu hình mã mẫu tin nhắn (Template ID) đã được ban kiểm duyệt Zalo phê duyệt cho từng sự kiện.')
                                    ->icon('heroicon-o-document-check')
                                    ->collapsible()
                                    ->schema([
                                        // Event 1: Booking
                                        Grid::make(2)->schema([
                                            Toggle::make('zalo_notify_booking_enabled')
                                                ->label('1. Gửi tin Zalo ZNS khi có Đơn đặt lịch mới')
                                                ->live()
                                                ->columnSpanFull(),

                                            TextInput::make('zalo_template_booking_id')
                                                ->label('Mã Template ID (Đặt Lịch)')
                                                ->placeholder('Ví dụ: 283921')
                                                ->visible(fn (Get $get) => $get('zalo_notify_booking_enabled')),

                                            Textarea::make('zalo_template_booking_content')
                                                ->label('Ghi chú nội dung mẫu ZNS')
                                                ->rows(2)
                                                ->visible(fn (Get $get) => $get('zalo_notify_booking_enabled')),
                                        ]),

                                        // Event 2: Login Alert
                                        Grid::make(2)->schema([
                                            Toggle::make('zalo_notify_login_enabled')
                                                ->label('2. Gửi tin Zalo khi có Đăng nhập Admin mới')
                                                ->live()
                                                ->columnSpanFull(),

                                            TextInput::make('zalo_template_login_id')
                                                ->label('Mã Template ID (Đăng Nhập)')
                                                ->placeholder('Ví dụ: 283922')
                                                ->visible(fn (Get $get) => $get('zalo_notify_login_enabled')),

                                            Textarea::make('zalo_template_login_content')
                                                ->label('Ghi chú nội dung mẫu ZNS')
                                                ->rows(2)
                                                ->visible(fn (Get $get) => $get('zalo_notify_login_enabled')),
                                        ]),

                                        // Event 3: Status Changed
                                        Grid::make(2)->schema([
                                            Toggle::make('zalo_notify_status_enabled')
                                                ->label('3. Gửi tin Zalo khi Cập nhật trạng thái Lịch hẹn')
                                                ->live()
                                                ->columnSpanFull(),

                                            TextInput::make('zalo_template_status_id')
                                                ->label('Mã Template ID (Trạng Thái)')
                                                ->placeholder('Ví dụ: 283923')
                                                ->visible(fn (Get $get) => $get('zalo_notify_status_enabled')),

                                            Textarea::make('zalo_template_status_content')
                                                ->label('Ghi chú nội dung mẫu ZNS')
                                                ->rows(2)
                                                ->visible(fn (Get $get) => $get('zalo_notify_status_enabled')),
                                        ]),
                                    ]),

                                // 3.3 Detail Guide Section (Collapsible & Collapsed)
                                Section::make('ℹ️ Điều Kiện Triển Khai Zalo Notification Service (ZNS)')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        \Filament\Forms\Components\View::make('filament.pages.partials.zalo-guide')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    /**
     * Lưu toàn bộ cấu hình vào Database.
     */
    public function save(): void
    {
        $data = $this->form->getState();

        $mapping = [
            // Mail Credentials
            'mail_mailer' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Phương thức gửi Mail (Mailer)'],
            'mail_host' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Máy chủ SMTP Host'],
            'mail_port' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Cổng kết nối SMTP Port'],
            'mail_encryption' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Giao thức mã hóa SMTP Encryption'],
            'mail_username' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Tài khoản đăng nhập SMTP'],
            'mail_password' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Mật khẩu SMTP / App Password'],
            'mail_from_address' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Email gửi đi hiển thị (From Address)'],
            'mail_from_name' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Tên người gửi hiển thị (From Name)'],

            // Mail Events & Templates
            'mail_notify_booking_enabled' => ['group' => 'mail', 'type' => 'toggle', 'desc' => 'Bật gửi email khi có đơn đặt lịch mới'],
            'mail_notify_booking_subject' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Tiêu đề email đặt lịch mới'],
            'mail_notify_booking_body' => ['group' => 'mail', 'type' => 'textarea', 'desc' => 'Mẫu nội dung email đặt lịch mới'],
            'mail_notify_login_enabled' => ['group' => 'mail', 'type' => 'toggle', 'desc' => 'Bật gửi email cảnh báo đăng nhập'],
            'mail_notify_login_subject' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Tiêu đề email cảnh báo đăng nhập'],
            'mail_notify_login_body' => ['group' => 'mail', 'type' => 'textarea', 'desc' => 'Mẫu nội dung email cảnh báo đăng nhập'],
            'mail_notify_status_enabled' => ['group' => 'mail', 'type' => 'toggle', 'desc' => 'Bật gửi email cập nhật trạng thái đơn hẹn'],
            'mail_notify_status_subject' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Tiêu đề email cập nhật trạng thái đơn hẹn'],
            'mail_notify_status_body' => ['group' => 'mail', 'type' => 'textarea', 'desc' => 'Mẫu nội dung email cập nhật trạng thái đơn hẹn'],
            'mail_notify_password_reset_enabled' => ['group' => 'mail', 'type' => 'toggle', 'desc' => 'Bật gửi email đặt lại mật khẩu'],
            'mail_notify_password_reset_subject' => ['group' => 'mail', 'type' => 'text', 'desc' => 'Tiêu đề email đặt lại mật khẩu'],
            'mail_notify_password_reset_body' => ['group' => 'mail', 'type' => 'textarea', 'desc' => 'Mẫu nội dung email đặt lại mật khẩu'],

            // Telegram Credentials
            'telegram_notify_enabled' => ['group' => 'telegram', 'type' => 'toggle', 'desc' => 'Bật/Tắt hệ thống thông báo Telegram Bot'],
            'telegram_bot_token' => ['group' => 'telegram', 'type' => 'text', 'desc' => 'Token của Telegram Bot riêng'],
            'telegram_chat_id' => ['group' => 'telegram', 'type' => 'text', 'desc' => 'Chat ID Telegram nhận thông báo'],

            // Telegram Events & Templates
            'telegram_notify_booking_enabled' => ['group' => 'telegram', 'type' => 'toggle', 'desc' => 'Bật gửi tin Telegram khi có đơn đặt lịch mới'],
            'telegram_template_booking' => ['group' => 'telegram', 'type' => 'textarea', 'desc' => 'Mẫu tin nhắn Telegram đặt lịch mới'],
            'telegram_notify_login_enabled' => ['group' => 'telegram', 'type' => 'toggle', 'desc' => 'Bật gửi tin Telegram cảnh báo đăng nhập'],
            'telegram_template_login' => ['group' => 'telegram', 'type' => 'textarea', 'desc' => 'Mẫu tin nhắn Telegram cảnh báo đăng nhập'],
            'telegram_notify_status_enabled' => ['group' => 'telegram', 'type' => 'toggle', 'desc' => 'Bật gửi tin Telegram cập nhật trạng thái lịch hẹn'],
            'telegram_template_status' => ['group' => 'telegram', 'type' => 'textarea', 'desc' => 'Mẫu tin nhắn Telegram cập nhật trạng thái lịch hẹn'],

            // Zalo Credentials
            'zalo_notify_enabled' => ['group' => 'zalo', 'type' => 'toggle', 'desc' => 'Bật/Tắt thông báo Zalo ZNS'],
            'zalo_oa_id' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Zalo OA ID'],
            'zalo_app_id' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Zalo App ID'],
            'zalo_secret_key' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Zalo Secret Key'],
            'zalo_access_token' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Zalo Access Token'],

            // Zalo Events & Templates
            'zalo_notify_booking_enabled' => ['group' => 'zalo', 'type' => 'toggle', 'desc' => 'Bật gửi tin Zalo khi có đơn đặt lịch mới'],
            'zalo_template_booking_id' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Mã Template ID Zalo đặt lịch mới'],
            'zalo_template_booking_content' => ['group' => 'zalo', 'type' => 'textarea', 'desc' => 'Nội dung mẫu tin Zalo đặt lịch mới'],
            'zalo_notify_login_enabled' => ['group' => 'zalo', 'type' => 'toggle', 'desc' => 'Bật gửi tin Zalo cảnh báo đăng nhập'],
            'zalo_template_login_id' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Mã Template ID Zalo cảnh báo đăng nhập'],
            'zalo_template_login_content' => ['group' => 'zalo', 'type' => 'textarea', 'desc' => 'Nội dung mẫu tin Zalo cảnh báo đăng nhập'],
            'zalo_notify_status_enabled' => ['group' => 'zalo', 'type' => 'toggle', 'desc' => 'Bật gửi tin Zalo cập nhật trạng thái lịch hẹn'],
            'zalo_template_status_id' => ['group' => 'zalo', 'type' => 'text', 'desc' => 'Mã Template ID Zalo cập nhật trạng thái'],
            'zalo_template_status_content' => ['group' => 'zalo', 'type' => 'textarea', 'desc' => 'Nội dung mẫu tin Zalo cập nhật trạng thái'],
        ];

        foreach ($mapping as $key => $meta) {
            $value = $data[$key] ?? null;
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => $meta['group'],
                    'value' => $value,
                    'type' => $meta['type'],
                    'description' => $meta['desc'],
                ]
            );
        }

        Cache::forget('site_settings');
        MailSettingService::applyConfig();

        Notification::make()
            ->title('Đã lưu cấu hình thành công!')
            ->body('Các thiết lập Email, Telegram Bot, Zalo và mẫu nội dung thông báo đã được cập nhật.')
            ->success()
            ->send();
    }

    /**
     * Áp dụng mẫu cấu hình nhanh cho Email (1-Click Presets).
     */
    public function applyPreset(string $preset): void
    {
        $currentData = $this->form->getState();

        switch ($preset) {
            case 'gmail':
                $currentData['mail_mailer'] = 'smtp';
                $currentData['mail_host'] = 'smtp.gmail.com';
                $currentData['mail_port'] = '587';
                $currentData['mail_encryption'] = 'tls';
                break;

            case 'domain_mail_ssl':
                $currentData['mail_mailer'] = 'smtp';
                $currentData['mail_host'] = 'mail.yourdomain.com';
                $currentData['mail_port'] = '465';
                $currentData['mail_encryption'] = 'ssl';
                break;

            case 'domain_mail_tls':
                $currentData['mail_mailer'] = 'smtp';
                $currentData['mail_host'] = 'mail.yourdomain.com';
                $currentData['mail_port'] = '587';
                $currentData['mail_encryption'] = 'tls';
                break;

            case 'resend':
                $currentData['mail_mailer'] = 'smtp';
                $currentData['mail_host'] = 'smtp.resend.com';
                $currentData['mail_port'] = '465';
                $currentData['mail_encryption'] = 'ssl';
                $currentData['mail_username'] = 'resend';
                break;

            case 'brevo':
                $currentData['mail_mailer'] = 'smtp';
                $currentData['mail_host'] = 'smtp-relay.brevo.com';
                $currentData['mail_port'] = '587';
                $currentData['mail_encryption'] = 'tls';
                break;
        }

        $this->form->fill($currentData);

        Notification::make()
            ->title('Đã áp dụng mẫu cấu hình nhanh!')
            ->body('Các thông số máy chủ và cổng đã được điền tự động. Vui lòng nhập tài khoản và mật khẩu của bạn rồi bấm Lưu.')
            ->info()
            ->send();
    }

    /**
     * Gửi email thử nghiệm để kiểm tra cấu hình SMTP.
     */
    public function sendTestEmail(?string $recipient = null): void
    {
        // Tự động lưu form trước khi test để lấy dữ liệu mới nhất
        $this->save();

        $toEmail = trim((string) ($recipient ?: ($this->form->getState()['test_email_recipient'] ?? auth()->user()?->email)));
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->title('Email nhận không hợp lệ')
                ->body('Vui lòng nhập địa chỉ email hợp lệ để nhận thư kiểm tra.')
                ->danger()
                ->send();
            return;
        }

        $result = MailSettingService::sendTestMail($toEmail);

        if ($result['success']) {
            Notification::make()
                ->title('Kết nối Email thành công!')
                ->body($result['message'])
                ->success()
                ->duration(8000)
                ->send();
        } else {
            Notification::make()
                ->title('Kết nối Email thất bại')
                ->body($result['message'])
                ->danger()
                ->duration(10000)
                ->send();
        }
    }

    /**
     * Gửi tin nhắn thử nghiệm tới Telegram Bot.
     */
    public function sendTestTelegram(): void
    {
        // Tự động lưu form trước khi test
        $this->save();

        $data = $this->form->getState();
        $token = $data['telegram_bot_token'] ?? '';
        $chatId = $data['telegram_chat_id'] ?? '';

        $result = TelegramNotificationService::sendTestNotification($token, $chatId);

        if ($result['success']) {
            Notification::make()
                ->title('Gửi Telegram thành công!')
                ->body($result['message'])
                ->success()
                ->duration(8000)
                ->send();
        } else {
            Notification::make()
                ->title('Gửi Telegram thất bại')
                ->body($result['message'])
                ->danger()
                ->duration(10000)
                ->send();
        }
    }
}
