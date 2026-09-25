# BÁO CÁO TOÀN DIỆN: MAKEUP-ARTIST PROJECT AUDIT
**Dự án:** Makeup Artist Web Application & Admin Management System  
**Ngày thực hiện:** 12/03/2026  
**Thư mục mã nguồn:** `C:\Users\trant\Desktop\makeup-artist`  
**Mục tiêu:** Đánh giá hiện trạng kiến trúc, cơ sở dữ liệu, bảo mật, hiệu năng và lập kế hoạch triển khai máy chủ độc lập (Server 24/7).

---

## 1. Executive Summary

Dự án **Makeup Artist** là một hệ thống web thương hiệu cá nhân kết hợp quản lý kinh doanh dịch vụ trang điểm, vận hành trên nền tảng **PHP 8.2+ / Laravel 12** và hệ thống quản trị cao cấp **Filament v3**. 

Frontend công khai phục vụ việc giới thiệu nghệ sĩ, quảng bá bảng giá dịch vụ theo cấp độ (Basic, Premium, Extra), hiển thị bộ sưu tập ảnh/video (Portfolio phong cách Masonry), tạp chí làm đẹp (Blog chia sẻ kiến thức) và form đặt lịch hẹn trực tuyến. Backend quản trị (Filament Admin Panel) cung cấp đầy đủ các phân hệ quản lý nội dung website (Banners, Services, Posts, Portfolios, Categories, Settings), quản lý đơn đặt lịch (Bookings, Booking Items, in hóa đơn), quản lý tài chính dòng tiền (Thu thanh toán, Chi phí mua sắm mỹ phẩm/dụng cụ) kèm Dashboard biểu đồ phân tích doanh thu.

Mặc dù hệ thống đã hoàn thiện giao diện Blade và các CRUD Resource cơ bản, qua phân tích chuyên sâu cho thấy dự án đang tồn tại một số **lỗi logic nghiêm trọng (vỡ route dịch vụ chi tiết, Seeder hỏng không chạy được)**, **lỗ hổng bảo mật rủi ro cao (lộ dữ liệu hóa đơn công khai IDOR, thiếu Rate Limiting, thiếu Policy cho module tài chính)**, và **vấn đề kiến trúc - hiệu năng đáng kể (gọi API nén ảnh và gửi mail đồng bộ trong vòng đời lưu dữ liệu, truy vấn database trực tiếp trong ServiceProvider boot, nhúng Tailwind CDN thay vì Vite compiled)**.

---

## 2. Technology Stack

| Thành phần | Version / Công nghệ | Trạng thái | Ghi chú |
|---|---|---|---|
| **Core Framework** | Laravel 12.54.1 | Ổn định | Phiên bản phát hành mới nhất của Laravel |
| **PHP Runtime** | PHP 8.5.4 (Herd local) / Req: ^8.2 | Tương thích | Đạt chuẩn PHP 8.2+ |
| **Admin Panel** | Filament v3.3.49 | Hoạt động tốt | Tích hợp TALL Stack (Tailwind, Alpine, Livewire 3) |
| **Admin Authorization** | Filament Shield 3.9.10 + Spatie Permission | Cần bổ sung | Thiếu Policy cho Expense & Payment |
| **Database** | MySQL (hoặc SQLite local) | Cần tối ưu | Chuyển đổi từ `database.sqlite` sang MySQL production |
| **Frontend Framework** | Blade Templates + Tailwind CSS | Cần dọn dẹp | Đang load Play CDN (`cdn.tailwindcss.com`) thay vì Vite |
| **Asset Bundler** | Vite 7.0.7 + `@tailwindcss/vite` 4.0 | Đã cấu hình | Chưa được nạp đầy đủ trong file layout `app.blade.php` |
| **Frontend Libraries** | AOS, GLightbox, Swiper, PureCounter, ImgComparisonSlider | Hoạt động | Đang nạp từ Unpkg / JSDelivr CDN |
| **Image Compression** | Tinify (TinyPNG) 1.6.4 | Rủi ro hiệu năng | Gọi API đồng bộ trong Model Eloquent Hooks |
| **Financial Charting** | `flowframe/laravel-trend` 0.4.0 | Hoạt động tốt | Dùng vẽ biểu đồ Doanh thu/Chi phí theo tháng |
| **Language Switcher** | `filament-language-switch` 3.1.1 | Hoạt động một phần | Chưa có bộ ngôn ngữ `lang/vi` tiêu chuẩn |
| **Test Suite** | PHPUnit 11.5.3 | Chưa khai thác | Chỉ có 2 file ExampleTest mặc định (0% test nghiệp vụ) |

---

## 3. Project Architecture

Hệ thống được tổ chức theo kiến trúc **Monolithic MVC** đặc trưng của Laravel 12 kết hợp **Filament Panel**:

```
Client (Browser)
 │
 ├── [Public Web Routes] ──> Controllers (Http/Controllers) ──> Views (resources/views/*.blade.php)
 │                                │
 │                                ├── [Models] ──> Database (MySQL)
 │                                └── [Shared Settings] <── AppServiceProvider (View::share)
 │
 └── [Admin Routes /admin] ──> AdminPanelProvider (Filament v3 / Livewire 3)
                                  │
                                  ├── Filament Resources & Pages (app/Filament/Resources/*)
                                  ├── Filament Widgets (DashboardStats, FinancialChart)
                                  ├── Spatie / Filament Shield Policies (app/Policies/*)
                                  └── Models & Observers (app/Models/* with Tinify booted hooks)
```

- **HTTP Layer:** Xử lý các request công khai qua 5 Controller chính (`HomeController`, `ServiceController`, `PostController`, `PortfolioController`, `BookingController`). Chưa sử dụng Form Request riêng; validation đang viết trực tiếp trong Controller.
- **Admin Layer:** Toàn bộ backend quản trị được đóng gói trong `app/Filament/` với 10 Resource độc lập, 2 Dashboard Widget và cơ chế phân quyền dựa trên `filament-shield`.
- **Event & Notification Layer:** Đã đăng ký Listener `SendLoginAlert` lắng nghe event `Illuminate\Auth\Events\Login` để gửi mail cảnh báo đăng nhập và định vị IP.
- **Service Layer / Model Hooks:** Chưa tách riêng Service Layer; logic phụ (nén ảnh TinyPNG) đang nằm trực tiếp trong phương thức `booted()` của 7 Model Eloquent.

---

## 4. Existing Features

### 4.1. Public Website
- **Trang chủ (`/`):**
  - Banner Hero Slider / Background động từ database.
  - Slogan thương hiệu và giới thiệu phong cách Nude Luxury.
  - Top 3 dịch vụ nổi bật (`is_featured = true`).
  - Khung so sánh hình ảnh Before/After thần thái (`img-comparison-slider`).
  - 3 bài viết Tạp chí làm đẹp mới nhất.
  - Popup khuyến mãi / thông báo toàn trang (khi bật `popup_active`).
- **Trang Giới thiệu (`/gioi-thieu`):**
  - Câu chuyện thương hiệu, hình ảnh nghệ sĩ, chữ ký điện tử.
  - Thước phim cảm hứng (Popup Video qua GLightbox).
  - Khung số liệu thành tựu (Đếm số động qua PureCounter: Số cô dâu, Số sự kiện, Số năm kinh nghiệm).
  - Timeline hành trình chinh phục nghề nghiệp.
  - Khóa đào tạo học viện (Academy).
- **Trang Dịch vụ & Báo giá (`/services`):**
  - Bảng giá phân cấp dịch vụ (Basic, Premium, Extra) hiển thị kèm tính năng / quyền lợi.
  - Quy trình làm việc 4 bước chuẩn mực.
  - Khung lưu ý quan trọng từ Admin.
  - *Lưu ý:* Route xem chi tiết dịch vụ `/services/{slug}` đang bị lỗi runtime (chưa có controller method).
- **Trang Bộ sưu tập / Portfolio (`/portfolio`):**
  - Lưới hình ảnh phong cách Masonry đa cột.
  - Hỗ trợ cả 2 định dạng: Hình ảnh (mở phóng to bằng Lightbox) và Video ngắn MP4 (tự động phát lặp).
  - Bộ lọc tức thì theo Danh mục (Ajax/DOM filter).
- **Trang Đặt lịch (`/booking`):**
  - Form đặt lịch hẹn trực tuyến: Tên, SĐT, Zalo, Link MXH, Ngày dự kiến, Chọn nhiều dịch vụ theo nhóm checkbox, Lời nhắn.
  - Bản đồ Google Maps nhúng.
  - Thông báo gửi lịch thành công.
- **Trang Tạp chí / Blog (`/posts` & `/posts/{slug}`):**
  - Danh sách bài viết có phân trang (9 bài/trang) và lọc theo chuyên mục bài viết.
  - Khung bài viết nổi bật (Featured Post).
  - Trang đọc bài viết chi tiết, định dạng Typography sang trọng (Drop cap, Blockquote), bài viết liên quan, tác giả, nút chia sẻ MXH.
- **Trang Hóa đơn khách hàng (`/booking/{booking}/invoice`):**
  - Giao diện hóa đơn thanh toán chi tiết từng dịch vụ, ngày thực hiện, số lượng, đơn giá, tổng tiền.
  - Tính năng In hóa đơn (`window.print()`) và Xuất file ảnh hóa đơn trực tiếp (`html2canvas`).

### 4.2. Admin Panel (`/admin`)
- **Dashboard:**
  - 3 Thẻ thống kê: Đơn chờ xử lý, Lợi nhuận tháng này (Thu trừ Chi), Công nợ cần thu.
  - Biểu đồ cột phân tích Doanh thu vs Chi phí 12 tháng trong năm (`FinancialChart`).
- **Quản lý Website (Website Management):**
  - `BannerResource`: Quản lý banner slider, popup, vị trí hiển thị, trạng thái bật/tắt.
  - `BookingResource`: Quản lý danh sách đặt lịch, chuyển trạng thái (pending, confirmed, completed, canceled), cập nhật tổng tiền hóa đơn, lịch trình chi tiết (Repeater lưu bảng `booking_items`), nút In hóa đơn nhanh.
  - `CategoryResource`: Quản lý danh mục dùng chung (phân loại thành 3 type: `service`, `portfolio`, `post`), slug tự động.
  - `PortfolioResource`: Quản lý tệp ảnh/video bộ sưu tập, chọn danh mục, resize ảnh tự động.
  - `PostResource`: Quản lý bài viết blog, Rich Editor có đính kèm file, ảnh thumbnail, trạng thái nháp/xuất bản, gán cờ nổi bật.
  - `ServiceResource`: Quản lý gói dịch vụ, giá hiển thị, phân cấp dịch vụ (Basic/Premium/Extra), thẻ quyền lợi (TagsInput), cờ hiển thị trang chủ (`is_featured`).
- **Quản lý Tài chính (Financial Management):**
  - `ExpenseResource`: Quản lý khoản chi nội bộ (tên sản phẩm, phân loại mỹ phẩm/dụng cụ/mặt bằng, số tiền, ngày chi, link mua hàng Shopee/FB, ảnh sản phẩm mua lại, ảnh biên lai).
  - `PaymentResource`: Quản lý khoản thu gắn liền với từng Booking (chọn đơn đặt lịch, tiêu đề thu như cọc/tất toán, hình thức CK/tiền mặt, ngày giờ thu, ảnh bill chuyển khoản).
- **Hệ thống (System Management):**
  - `UserResource`: Quản lý tài khoản Admin, mật khẩu, ảnh đại diện Avatar, trạng thái xác thực email.
  - `SettingResource`: Quản lý key-value toàn bộ cấu hình hệ thống (Logo, Favicon, Hotline, Địa chỉ, Bản đồ, Slogan, Video, Dữ liệu đếm số, Popup).
  - `Roles & Permissions (Filament Shield)`: Quản lý vai trò và quyền hạn chi tiết cho từng Resource, Page, Widget.

### 4.3. Các thành phần chưa thấy implementation
- *Hệ thống Giỏ hàng / Đặt hàng sản phẩm trực tuyến (E-commerce cart/checkout)*: Chưa thấy implementation trong source code (hiện chỉ có quản lý mua sắm chi phí nội bộ).
- *Cổng thanh toán tự động (VNPAY, MoMo, VietQR động)*: Chưa thấy implementation trong source code (hiện thu tiền thủ công và lưu ảnh minh chứng).
- *Hệ thống API dành cho Mobile App / bên thứ ba*: Chưa thấy implementation trong source code (chưa có file `routes/api.php`).

---

## 5. Database Architecture

### 5.1. Danh sách các bảng trong hệ thống

1. **`users`**: Tài khoản quản trị viên.
   - `id`, `name`, `email`, `email_verified_at`, `password`, `avatar` (nullable), `remember_token`, `timestamps`.
2. **`settings`**: Cấu hình hệ thống động.
   - `id`, `group`, `key` (unique), `description` (nullable), `value` (longText, nullable), `type`, `timestamps`.
3. **`banners`**: Banner slider và popup.
   - `id`, `title`, `image_path`, `image_mobile_path` (nullable), `link_url` (nullable), `position`, `is_active`, `order`, `timestamps`.
4. **`categories`**: Danh mục phân loại đa năng.
   - `id`, `name`, `slug` (unique), `type` (mặc định 'post'; các giá trị: `service`, `portfolio`, `post`), `order`, `timestamps`.
5. **`services`**: Bảng giá và gói dịch vụ.
   - `id`, `category_id` (nullable, FK -> categories.id), `name`, `price_text`, `service_level` (nullable: Basic, Premium, Extra), `description` (nullable), `features` (JSON / array), `is_active`, `is_featured`, `timestamps`.
6. **`portfolios`**: Tác phẩm bộ sưu tập.
   - `id`, `category_id` (nullable, FK -> categories.id), `title` (nullable), `type` (image/video), `file_path`, `is_active`, `sort_order`, `timestamps`.
7. **`posts`**: Bài viết tạp chí làm đẹp.
   - `id`, `category_id` (FK -> categories.id), `title`, `slug` (unique), `excerpt` (nullable), `content` (longText), `thumbnail` (nullable), `status`, `is_featured`, `published_at` (nullable), `timestamps`.
8. **`bookings`**: Đơn đặt lịch của khách hàng.
   - `id`, `customer_name`, `phone`, `zalo` (nullable), `social_link` (nullable), `booking_date` (datetime, nullable), `service_ids` (JSON mảng ID dịch vụ quan tâm), `message` (nullable), `status` (pending, confirmed, completed, canceled), `notes` (nullable), `total_amount` (integer), `timestamps`.
9. **`booking_items`**: Chi tiết lịch trình và từng hạng mục dịch vụ theo đơn.
   - `id`, `booking_id` (FK -> bookings.id, cascade delete), `service_name`, `service_date` (datetime, nullable), `description` (nullable), `price` (bigInteger), `quantity` (integer), `timestamps`.
10. **`payments`**: Các giao dịch thu tiền thực tế theo hợp đồng/đơn booking.
    - `id`, `booking_id` (FK -> bookings.id, cascade delete), `title`, `amount` (integer), `payment_method` (nullable), `proof_image` (nullable), `payment_date` (datetime), `notes` (nullable), `timestamps`.
11. **`expenses`**: Chi phí mua sắm mỹ phẩm, dụng cụ, vận hành nội bộ.
    - `id`, `item_name`, `category` (nullable), `amount` (integer), `expense_date` (date), `product_image` (nullable), `receipt_image` (nullable), `buy_link` (nullable), `notes` (nullable), `timestamps`.
12. **Bảng phân quyền & Session/Jobs:**
    - `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` (Spatie / Shield).
    - `sessions`, `jobs`, `job_batches`, `failed_jobs`, `cache`, `cache_locks`.

### 5.2. Sơ đồ thực thể quan hệ logic (Text ERD)

```
User
 ├── morphToMany → Roles (Spatie)
 └── morphToMany → Permissions (Spatie)

Category
 ├── hasMany → Posts (posts.category_id)
 ├── hasMany → Services (services.category_id)
 └── hasMany → Portfolios (portfolios.category_id)

Service
 └── belongsTo → Category (nullable)

Post
 └── belongsTo → Category

Portfolio
 └── belongsTo → Category (nullable)

Booking
 ├── hasMany → BookingItem (cascade on delete)
 └── hasMany → Payment (cascade on delete)

BookingItem
 └── belongsTo → Booking

Payment
 └── belongsTo → Booking

Expense (Bảng độc lập - Quản lý dòng tiền chi)

Banner (Bảng độc lập - Quản lý hiển thị media)

Setting (Bảng độc lập - Key/Value cấu hình toàn hệ thống)
```

---

## 6. Route Architecture

### 6.1. Bảng phân loại Routes

| Phương thức | URI | Tên Route | Controller / Action | Middleware | Đánh giá Authorization |
|---|---|---|---|---|---|
| **GET** | `/` | `home` | `HomeController@index` | `web` | Public |
| **GET** | `/gioi-thieu` | `about` | `HomeController@about` | `web` | Public |
| **GET** | `/services` | `services.index` | `ServiceController@index` | `web` | Public |
| **GET** | `/services/{slug}` | `services.show` | `ServiceController@show` | `web` | **LỖI: Controller thiếu method `show`** |
| **GET** | `/posts` | `posts.index` | `PostController@index` | `web` | Public |
| **GET** | `/posts/{slug}` | `posts.show` | `PostController@show` | `web` | Public |
| **GET** | `/portfolio` | `portfolio.index` | `PortfolioController@index` | `web` | Public |
| **GET** | `/booking` | `booking.index` | `BookingController@index` | `web` | Public |
| **POST** | `/booking` | `booking.store` | `BookingController@store` | `web` | **Public - Thiếu Rate Limit** |
| **GET** | `/booking/{booking}/invoice` | `booking.invoice` | Closure view `invoice` | `web` | **RỦI RO: Không Auth, lộ thông tin khách** |
| **GET** | `/up` | health check | Laravel internal | `web` | Public |
| **ANY** | `/admin/*` | Filament panel | Filament Admin Controller | `web`, `auth:web` | Bảo vệ bởi Filament Session Auth & Shield |

---

## 7. Authentication & Authorization

### 7.1. Cơ chế Authentication
- Xác thực sử dụng Guard mặc định `web` của Laravel (Session Driver + Eloquent User Provider).
- Trang đăng nhập, đổi mật khẩu và xác minh email do **Filament** quản lý tại endpoint `/admin/login`, `/admin/password-reset/*`, `/admin/email-verification/*`.
- Model `User` triển khai `MustVerifyEmail` và `HasAvatar`. Khi tài khoản mới được tạo, hệ thống tự động kích hoạt gửi email xác thực (`sendEmailVerificationNotification`).
- Sau khi đăng nhập thành công, Listener `SendLoginAlert` gửi email thông báo chi tiết thời gian, IP, vị trí địa lý và thiết bị.

### 7.2. Cơ chế Authorization
- Phân quyền sử dụng `bezhansalleh/filament-shield` kết hợp `spatie/laravel-permission`.
- 9 Policy đã được sinh ra trong `app/Policies/`: `BannerPolicy`, `BookingPolicy`, `CategoryPolicy`, `PortfolioPolicy`, `PostPolicy`, `RolePolicy`, `ServicePolicy`, `SettingPolicy`, `UserPolicy`.
- **Lỗ hổng phân quyền phát hiện được:**
  - `ExpensePolicy` và `PaymentPolicy` **chưa tồn tại**. Filament Shield không thể kiểm soát quyền truy cập chi tiết cho phân hệ Chi phí và Thu tiền nếu có nhiều tài khoản phân quyền khác nhau.
  - Route `/booking/{booking}/invoice` nằm hoàn toàn ngoài phạm vi bảo vệ của middleware auth/policy.

---

## 8. Code Quality

### CRITICAL
1. **Lỗi Runtime Route Dịch vụ Chi tiết (`routes/web.php` & `ServiceController.php`):**
   - Route `Route::get('/services/{slug}', [ServiceController::class, 'show'])` trỏ tới phương thức `show()` trong `ServiceController`.
   - Trong `app/Http/Controllers/ServiceController.php` chỉ có duy nhất phương thức `index()`.
   - *Hậu quả:* Bất kỳ ai click vào link xem chi tiết dịch vụ đều gặp lỗi `BadMethodCallException: Method App\Http\Controllers\ServiceController::show does not exist.` (500 Error).
2. **DatabaseSeeder Bị Lỗi Cú Pháp / Lệch Cột (`database/seeders/DatabaseSeeder.php`):**
   - Seeder cố gắng ghi dữ liệu vào cột `portfolios.category` (đã bị xóa ở migration đổi sang `category_id`) và `bookings.service_id` (đã bị xóa ở migration đổi sang `service_ids`).
   - *Hậu quả:* Lệnh `php artisan db:seed` lập tức văng lỗi SQL `Column not found`. Không thể khởi tạo dữ liệu mẫu cho môi trường mới.

### HIGH
1. **Vi phạm nguyên tắc DRY & Synchronous Blocking I/O trong 7 Model (`app/Models/*`):**
   - 7 Model (`Banner`, `Expense`, `Payment`, `Portfolio`, `Post`, `Setting`, `User`) copy-paste lặp lại chính xác đoạn mã nén ảnh TinyPNG trong hàm `booted()`.
   - Đoạn mã sử dụng `set_time_limit(120)` và gọi HTTP đồng bộ ra server bên ngoài. Nếu mạng chập chờn, thao tác lưu bản ghi trong Admin sẽ bị treo tới 2 phút.
2. **Gãy cấu hình khi chạy `config:cache` (`app/Models/*`):**
   - Cả 7 Model đều gọi trực tiếp `env('TINYPNG_API_KEY')` thay vì `config(...)`.
   - Theo chuẩn Laravel, khi chạy `php artisan config:cache` trên Server Production, toàn bộ hàm `env()` sẽ trả về `null`. Khi đó việc nén ảnh hoàn toàn thất bại.
3. **Treo ứng dụng khi MySQL chưa sẵn sàng (`app/Providers/AppServiceProvider.php`):**
   - `AppServiceProvider::boot()` thực thi `Schema::hasTable('settings')` và `Setting::pluck(...)` trực tiếp trên mọi chu kỳ khởi động app (kể cả khi chạy lệnh CLI `artisan`). Nếu cơ sở dữ liệu chưa sẵn sàng hoặc kết nối chậm, các lệnh `artisan` sẽ bị đứng chờ connection timeout.

### MEDIUM
1. **Lỗi logic tổng hợp số liệu theo tháng (`app/Filament/Widgets/DashboardStats.php`):**
   - Sử dụng `whereMonth('payment_date', now()->month)` mà không giới hạn năm (`whereYear`). Dữ liệu tháng 3 của năm 2025 và 2026 sẽ bị cộng gộp chung vào nhau.
2. **Truy vấn thiếu phân trang (`app/Http/Controllers/PortfolioController.php`):**
   - Lấy toàn bộ portfolio bằng `Portfolio::with('category')->get()` đưa ra view. Khi số lượng ảnh/video tăng lên hàng trăm tệp, trang web sẽ bị giật lag và tốn RAM máy chủ.
3. **Đăng ký trùng lặp Plugin (`app/Providers/Filament/AdminPanelProvider.php`):**
   - `\BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()` được khai báo 2 lần trong mảng `->plugins([...])`.
4. **Thiếu Form Request:**
   - Việc validate dữ liệu form đặt lịch đang viết trực tiếp trong `BookingController@store`, chưa tách thành `StoreBookingRequest`.

### LOW
1. **Truy vấn thừa Settings (`app/Http/Controllers/HomeController.php`):**
   - Phương thức `about()` query lại `Setting::pluck(...)` trong khi biến `$settings` đã được `AppServiceProvider` chia sẻ toàn cục cho toàn bộ view qua `View::share`.
2. **Hardcoded text và thiếu file `lang/vi`:**
   - Cấu hình locale là `vi` nhưng chưa có thư mục `lang/vi` chuẩn hóa cho các thông báo validation mặc định của Laravel.

---

## 9. Security Audit

### CRITICAL
1. **Lỗ hổng IDOR / Lộ thông tin hóa đơn công khai:**
   - **Vị trí:** `routes/web.php` dòng 53-56 (`/booking/{booking}/invoice`) và `BookingResource.php` dòng 226.
   - **Mô tả:** Endpoint xuất hóa đơn nhận trực tiếp Model Binding `$booking` mà không có middleware xác thực `auth`, không kiểm tra quyền và không dùng Signed URL hay token bí mật ngẫu nhiên (UUID).
   - **Mức độ rủi ro:** Bất kỳ ai chỉ cần đổi số ID trên trình duyệt (ví dụ `/booking/1/invoice`, `/booking/2/invoice`) là có thể đọc toàn bộ danh tính khách hàng, số điện thoại, ngày giờ đặt hẹn, lịch trình làm việc và số tiền giao dịch.

### HIGH
1. **Thiếu Rate Limiting trên form Booking (`POST /booking`):**
   - Endpoint nhận dữ liệu đặt lịch công khai không có middleware `throttle`. Kẻ xấu hoặc bot có thể spam hàng ngàn request tạo đơn giả, gây nghẽn database và làm sai lệch thống kê tài chính.
2. **Treo Login và DoS cục bộ tại `SendLoginAlert` Listener:**
   - Khi Admin đăng nhập, listener thực hiện gọi HTTP ra `http://ip-api.com/json/{$ip}` (giao thức HTTP không mã hóa, không đặt timeout) và gọi `Mail::send()` đồng bộ. Nếu máy chủ gửi mail (SMTP) phản hồi chậm hoặc lỗi kết nối, quá trình đăng nhập bị treo cứng hoặc văng lỗi Exception.
3. **Thiếu Policy cho Module Tài chính (`Expense` & `Payment`):**
   - Chưa tạo `ExpensePolicy` và `PaymentPolicy`, dẫn đến việc phân quyền hạn chế theo vai trò trong Filament Shield không áp dụng được triệt để cho 2 module nhạy cảm này.

### MEDIUM
1. **File thực thi nhị phân `cloudflared.exe` bị commit vào Git:**
   - File nhị phân kích thước lớn 65.8MB (`cloudflared.exe`) nằm trong thư mục gốc và đang được theo dõi bởi Git.
   - *Rủi ro:* Làm phình to dung lượng repository, kéo dài thời gian git clone/pull trên server và tạo rủi ro an ninh khi lưu trữ file thực thi binary không rõ nguồn gốc trong mã nguồn.
2. **Mật khẩu khởi tạo mặc định trong Seeder:**
   - `DatabaseSeeder.php` chứa thông tin tài khoản mẫu và mật khẩu yếu (`bcrypt('123456')`).
3. **Tinify API Key không qua Config trung gian:**
   - Đang đọc trực tiếp từ môi trường `env()`, thiếu cơ chế fallback an toàn.

---

## 10. Performance Audit

### 10.1. Laravel & Backend Bottlenecks
1. **Nghẽn I/O do xử lý ảnh đồng bộ (Synchronous Image Processing):**
   - Khi Admin tải lên Banner, Tác phẩm hoặc Hóa đơn, Model thực hiện nén ảnh qua Cloud API của TinyPNG ngay trong chu kỳ Request-Response. Trình duyệt Admin phải chờ API bên ngoài xử lý xong mới nhận được phản hồi.
   - *Giải pháp:* Chuyển logic nén ảnh thành Laravel **Queue Job** chạy ngầm.
2. **Nghẽn `information_schema` do `Schema::hasTable` trên mọi Request:**
   - `AppServiceProvider::boot()` truy vấn kiểm tra bảng `settings` và nạp toàn bộ settings trên từng HTTP request.
   - *Giải pháp:* Sử dụng `Cache::rememberForever('site_settings', ...)` và chỉ xóa cache khi `SettingResource` cập nhật dữ liệu.
3. **Thiếu Eager Loading & Thiếu Pagination tại Portfolio:**
   - `PortfolioController@index` tải toàn bộ bản ghi mà không phân trang.

### 10.2. Database & MySQL Bottlenecks
1. **Thiếu Index trên các cột tìm kiếm và lọc thường xuyên:**
   - Bảng `bookings`: Thiếu index cho `status`, `booking_date`, `customer_name`, `phone`.
   - Bảng `payments`: Thiếu index cho `payment_date`, `booking_id`.
   - Bảng `expenses`: Thiếu index cho `expense_date`, `category`.
   - Bảng `posts`: Thiếu index cho `status`, `published_at`, `is_featured`.
2. **Sử dụng cột `JSON` cho `service_ids` thay vì quan hệ chuẩn:**
   - Bảng `bookings` lưu `service_ids` dưới dạng mảng JSON `[1, 2, 3]`. Khi cần thống kê số lượng đơn theo từng dịch vụ, câu truy vấn sẽ phải scan toàn bộ bảng thay vì dùng quan hệ `belongsToMany` có Index.

### 10.3. Frontend Bottlenecks
1. **Sử dụng Tailwind Play CDN trên Production:**
   - File `resources/views/layouts/app.blade.php` nạp `<script src="https://cdn.tailwindcss.com"></script>`.
   - Play CDN buộc trình duyệt phải tải script JavaScript nặng của Tailwind và tự biên dịch toàn bộ class CSS tại runtime của client, gây hiện tượng chớp nháy giao diện (FOUC), giật lag trên thiết bị di động yếu và tăng điểm số LCP (Largest Contentful Paint).
2. **Nạp thư viện qua nhiều CDN rời rạc:**
   - AOS, FontAwesome, ImgComparisonSlider, Swiper, GLightbox, PureCounter đang nạp từ các bên thứ ba (unpkg, cdnjs, jsdelivr) mà không bundle qua Vite.

---

## 11. Dependency Audit

### 11.1. Backend Dependencies (PHP / Composer)

| Package | Phiên bản khóa | Mục đích | Đánh giá / Khuyến nghị |
|---|---|---|---|
| `laravel/framework` | `v12.54.1` | Core Framework | Ổn định, tương thích hoàn toàn PHP 8.2 - 8.5 |
| `filament/filament` | `v3.3.49` | Admin Panel Core | Hoạt động tốt, tương thích Laravel 12 |
| `bezhansalleh/filament-shield` | `3.9.10` | Quản lý Phân quyền | Cần tạo đủ Policy cho các Resource còn thiếu |
| `bezhansalleh/filament-language-switch` | `3.1.1` | Đổi ngôn ngữ Admin | Hoạt động tốt |
| `flowframe/laravel-trend` | `v0.4.0` | Tính toán biểu đồ | Hoạt động tốt |
| `tinify/tinify` | `1.6.4` | SDK Nén ảnh TinyPNG | Cần chuyển sang chạy dạng Queue Job ngầm |
| `laravel/tinker` | `v2.10.1` | CLI Debugging | Tiêu chuẩn |

### 11.2. Frontend Dependencies (Node / NPM)

| Package | Phiên bản | Mục đích | Đánh giá / Khuyến nghị |
|---|---|---|---|
| `vite` | `^7.0.7` | Asset Bundler | Chuẩn hiện đại |
| `tailwindcss` | `^4.0.0` | Utility CSS | Bản v4 mới, cần build ra CSS tĩnh thay vì dùng Play CDN |
| `@tailwindcss/vite` | `^4.0.0` | Vite Plugin cho Tailwind | Đã cấu hình |
| `@tailwindcss/typography` | `^0.5.20` | Định dạng bài viết Blog | Đã cài |
| `@tailwindcss/forms` | `^0.5.11` | Styling Form | Đã cài |
| `axios` | `^1.11.0` | HTTP Client | Đã cấu hình |

---

## 12. Testing Status

- **Thực trạng:** Dự án hiện **chưa có bất kỳ bài kiểm tra tự động (Automated Test) nào** phục vụ nghiệp vụ thực tế.
- **File test hiện có:**
  - `tests/Feature/ExampleTest.php`: Chỉ kiểm tra `GET /` trả về status 200.
  - `tests/Unit/ExampleTest.php`: Chỉ kiểm tra `assertTrue(true)`.
- **Độ bao phủ (Test Coverage):** **0%**.
- **Các module quan trọng chưa có test:**
  - Luồng validate và lưu đơn đặt lịch (`BookingController@store`).
  - Phân quyền Admin và truy cập Resource qua Filament Shield.
  - Luồng tính toán doanh thu, chi phí, công nợ trên Dashboard Widget.
  - Khả năng xuất hóa đơn và bảo mật thông tin đơn hàng.

---

## 13. Production Readiness

### Đánh giá mức độ sẵn sàng: **45 / 100**

- **Development -> Staging:** Đang ở giai đoạn chuyển giao. Giao diện công khai và form Admin đã hoàn chỉnh về mặt hiển thị nhưng chưa tối ưu luồng xử lý ngầm.
- **Staging -> Production:** **Chưa đủ điều kiện đưa vào hoạt động chính thức**.

**Lý do chưa thể Go-Live ngay:**
1. Có route dịch vụ bị vỡ (`/services/{slug}`).
2. Lỗ hổng bảo mật hóa đơn công khai (IDOR) làm lộ dữ liệu khách hàng.
3. Thiếu Rate Limiting cho form gửi đặt lịch.
4. Lỗi runtime tiềm ẩn khi chạy `config:cache` do hàm `env()` trong Models.
5. Frontend đang phụ thuộc vào Play CDN thay vì asset compiled tĩnh.

---

## 14. Server Deployment Assessment

Khi triển khai trên **máy chủ riêng (Dedicated Server / VPS)** hoạt động 24/7, kiến trúc tổng thể cần thiết lập như sau:

```
                  [ Internet (Khách truy cập & Admin) ]
                                   │
                                   ▼
          [ Router / Port Forwarding (80, 443) / UFW Firewall ]
                                   │
                                   ▼
       [ Reverse Proxy & Web Server: Nginx (SSL Certbot Let's Encrypt) ]
                                   │
                    ┌──────────────┴──────────────┐
                    │ HTTP Request                │ Static Files
                    ▼                             ▼
       [ PHP-FPM (PHP 8.2 / 8.3) ]          [ public/build & public/storage ]
                    │
                    ▼
           [ Laravel 12 Core ]
              │            │
              ▼            ▼
      [ MySQL 8.0/MariaDB ]  [ Cache / Session Driver ]
              ▲
              │ (Job Processing)
      [ Supervisor: queue:work ] <── (Xử lý nén ảnh TinyPNG & gửi Mail)
              ▲
              │ (Scheduled Tasks)
      [ Linux Cron: schedule:run ]
```

### Các thành phần bắt buộc trên Server:
1. **Web Server:** Khuyến nghị **Nginx** (xử lý static assets tốt, cấu hình SSL Let's Encrypt đơn giản, ít tốn RAM hơn Apache).
2. **PHP Engine:** `php8.2-fpm` hoặc `php8.3-fpm` kèm các extension: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`, `gd`/`imagick`, `opcache`.
3. **Database:** `MySQL 8.0` hoặc `MariaDB 10.11+`.
4. **Process Manager (Supervisor):** Quản lý tiến trình chạy ngầm `php artisan queue:work` tự động khởi động lại nếu có sự cố.
5. **System Cron:** Chạy lệnh `php artisan schedule:run` mỗi phút.
6. **OPcache:** Bật OPcache trên PHP-FPM để tăng tốc độ thực thi mã nguồn lên gấp 3-5 lần mà không tốn thêm tài nguyên phần cứng.

---

## 15. Recommended Server Architecture

### Option A — Đơn giản, Tiết kiệm tài nguyên tối đa (Khuyến nghị cho phần cứng hạn chế)
- **Cấu hình máy chủ:** 1 Core CPU, 1GB - 2GB RAM, 20GB-30GB SSD.
- **Hệ điều hành:** Linux Ubuntu 22.04 LTS hoặc 24.04 LTS.
- **Ngăn xếp dịch vụ:**
  - Web Server: **Nginx**.
  - Runtime: **PHP-FPM 8.2 / 8.3** (bật OPcache).
  - Database: **MariaDB 10.11** (tối ưu cấu hình `innodb_buffer_pool_size = 256M`).
  - Cache / Session / Queue: Dùng trực tiếp driver `database` tích hợp sẵn của Laravel (không cần cài thêm Redis).
  - Queue Worker: Chạy 1 tiến trình `php artisan queue:work --queue=default --sleep=3 --tries=3` qua Supervisor.
- **Ưu điểm:**
  - Tiêu tốn cực kỳ ít RAM (tổng lượng RAM sử dụng toàn hệ thống ~500MB - 700MB).
  - Không phát sinh chi phí hoặc cấu hình Redis phức tạp.
  - Quản trị đơn giản, độ ổn định cao.
- **Nhược điểm:**
  - Queue và Session đọc/ghi trực tiếp vào MySQL, khi traffic đồng thời tăng đột biến (hàng trăm request/giây) sẽ tạo thêm một lượng I/O nhẹ lên database.

---

### Option B — Production Mở rộng, Hiệu năng cao (Khi có thêm tài nguyên)
- **Cấu hình máy chủ:** 2+ Core CPU, 4GB+ RAM, SSD NVMe.
- **Hệ điều hành:** Linux Ubuntu 22.04 LTS.
- **Ngăn xếp dịch vụ:**
  - Web Server: **Nginx** (kèm cấu hình gzip/brotli và rate limit ở tầng Nginx).
  - Runtime: **PHP-FPM** (bật OPcache + JIT).
  - In-memory Store: **Redis Server** (xử lý toàn bộ Session, Cache và Queue).
  - Database: **MySQL 8.0** tinh chỉnh buffer pool lớn.
  - Process Manager: Supervisor quản lý 2-4 queue worker song song.
- **Ưu điểm:**
  - Tốc độ phản hồi cực nhanh (<30ms) do Session và Cache nằm hoàn toàn trên RAM.
  - Database MySQL hoàn toàn giải phóng khỏi tác vụ đọc/ghi session và queue.
- **Nhược điểm:**
  - Cần tối thiểu 3GB - 4GB RAM để vận hành an toàn.
  - Cần bảo trì thêm dịch vụ Redis.

---

## 16. Critical Problems (Danh sách cần xử lý trước khi Public)

- [ ] **Lỗi 1 (Broken Method):** Bổ sung phương thức `show($slug)` vào `ServiceController.php` hoặc sửa lại route `/services/{slug}` để không gây crash 500.
- [ ] **Lỗi 2 (Seeder Crash):** Cập nhật `DatabaseSeeder.php` đồng bộ chuẩn xác với cấu trúc cột database hiện tại (`category_id` thay vì `category`, `service_ids` thay vì `service_id`).
- [ ] **Lỗi 3 (Lỗ hổng IDOR Hóa đơn):** Đặt route `/booking/{booking}/invoice` vào middleware `auth` cho Admin, hoặc sinh `uuid` / Signed URL dùng một lần nếu muốn gửi link cho khách hàng.
- [ ] **Lỗi 4 (Config Cache Bug):** Đưa key `TINYPNG_API_KEY` vào `config/services.php` và thay thế toàn bộ lệnh `env('TINYPNG_API_KEY')` bằng `config('services.tinypng.key')`.
- [ ] **Lỗi 5 (Nghẽn Login & Model Save):** Chuyển việc nén ảnh TinyPNG và gửi email Login Alert sang xử lý nền qua **Queue Jobs** (`ShouldQueue`).
- [ ] **Lỗi 6 (Thiếu Rate Limit):** Thêm middleware `throttle:10,1` vào route `POST /booking` để chống spam dữ liệu.
- [ ] **Lỗi 7 (Thiếu Policy Tài chính):** Tạo `ExpensePolicy` và `PaymentPolicy` để bảo vệ dữ liệu tài chính trong Filament Admin.
- [ ] **Lỗi 8 (Tailwind Play CDN):** Chuyển từ CDN sang asset build Vite (`@vite`) trong `app.blade.php`.
- [ ] **Lỗi 9 (Git Cleanup):** Loại bỏ file binary `cloudflared.exe` khỏi Git repository và cập nhật `.gitignore`.

---

## 17. Recommended Roadmap

```
PHASE 0: Backup & Safety
 └── Dọn dẹp Git (loại bỏ cloudflared.exe), backup source & DB.
PHASE 1: Critical Fixes
 └── Sửa ServiceController show method, sửa DatabaseSeeder, đóng lỗ hổng IDOR invoice.
PHASE 2: Architecture & Queue Refactoring
 └── Tách Queue Job cho TinyPNG, chuyển LoginAlert sang ShouldQueue, sửa AppServiceProvider boot query.
PHASE 3: Security Hardening
 └── Bổ sung ExpensePolicy & PaymentPolicy, Rate Limiting form Booking, cấu hình config('services.tinypng').
PHASE 4: Frontend & Performance Optimization
 └── Build asset tĩnh qua Vite, loại bỏ Play CDN, cấu hình Cache Settings & Index Database.
PHASE 5: Production Server Preparation
 └── Cấu hình Nginx, PHP-FPM, Supervisor, System Cron, SSL Certbot trên Server riêng.
PHASE 6: Feature Development
 └── Bắt đầu tiếp nhận và triển khai các tính năng mới theo yêu cầu của bạn.
PHASE 7: Monitoring & Backup Automation
 └── Thiết lập script tự động backup MySQL hàng ngày và log rotation.
```

### Chi tiết từng giai đoạn:

| Phase | Nhiệm vụ | Độ ưu tiên | File liên quan | Kết quả mong đợi | Rủi ro |
|---|---|---|---|---|---|
| **Phase 0** | Gỡ bỏ `cloudflared.exe` khỏi Git, thêm vào `.gitignore` | P0 | `.gitignore` | Repo sạch, dung lượng nhẹ, clone nhanh | Rất thấp |
| **Phase 1** | Sửa `ServiceController`, sửa `DatabaseSeeder`, bảo vệ route invoice | P0 | `ServiceController.php`, `DatabaseSeeder.php`, `web.php` | Không còn lỗi 500, seeder chạy mượt, bảo mật thông tin khách hàng | Rất thấp |
| **Phase 2** | Tạo Queue Job cho TinyPNG, chuyển Mail Login Alert sang Queue, cache Settings | P1 | `app/Jobs/CompressImageJob.php`, `SendLoginAlert.php`, `AppServiceProvider.php` | Thao tác Admin lưu ảnh phản hồi ngay lập tức, login nhanh, không nghẽn DB | Thấp |
| **Phase 3** | Tạo Policy cho Expense & Payment, thêm throttle cho form booking | P1 | `app/Policies/*`, `web.php`, `config/services.php` | Phân quyền tài chính chặt chẽ, chống bot spam đơn | Thấp |
| **Phase 4** | Build Tailwind qua Vite, loại bỏ Play CDN, thêm DB Index cho các bảng | P2 | `app.blade.php`, `database/migrations/*` | Trang web load mượt mà, điểm Google PageSpeed cao, truy vấn nhanh | Cần test kỹ CSS |
| **Phase 5** | Cấu hình Nginx, PHP-FPM, Supervisor Worker, Cron trên Server | P2 | Config máy chủ | Website chạy ổn định 24/7 trên máy chủ riêng | Cần cấu hình mạng đúng |
| **Phase 6** | Phát triển các tính năng và giao diện mới theo yêu cầu người dùng | P3 | Theo từng task | Đáp ứng mở rộng kinh doanh | Theo từng task |
| **Phase 7** | Tự động hóa backup Database và xoay vòng logs | P3 | Server crontab & script backup | Đảm bảo an toàn dữ liệu tuyệt đối | Rất thấp |

---

## 18. Files That Require Special Attention

1. **`routes/web.php`**: Chứa route hỏng `services.show`, route lộ hóa đơn IDOR `booking.invoice` và route booking thiếu throttle.
2. **`app/Http/Controllers/ServiceController.php`**: Đang thiếu phương thức `show()` được định nghĩa ở route.
3. **`database/seeders/DatabaseSeeder.php`**: Đang chứa câu lệnh chèn dữ liệu vào các cột đã bị xóa bỏ trong migration.
4. **`app/Providers/AppServiceProvider.php`**: Đang thực thi truy vấn trực tiếp vào database trên mọi chu kỳ khởi động app.
5. **`app/Listeners/SendLoginAlert.php`**: Chứa lệnh gọi HTTP và Mail đồng bộ, gây nguy cơ treo màn hình đăng nhập.
6. **`app/Models/User.php`**, **`Banner.php`**, **`Portfolio.php`**, **`Post.php`**, **`Payment.php`**, **`Expense.php`**, **`Setting.php`**: Cả 7 model đều gọi `env('TINYPNG_API_KEY')` và xử lý nén ảnh đồng bộ trong `booted()`.
7. **`resources/views/layouts/app.blade.php`**: Đang nhúng Tailwind Play CDN thay vì asset biên dịch tĩnh qua Vite.
8. **`app/Providers/Filament/AdminPanelProvider.php`**: Khai báo plugin FilamentShield bị trùng lặp.
9. **`app/Filament/Widgets/DashboardStats.php`**: Logic lọc theo tháng thiếu điều kiện năm.

---

## 19. Unknowns / Questions

Dưới đây là các điểm **không thể xác định chính xác chỉ từ mã nguồn hiện tại** và cần bạn xác nhận khi bước vào giai đoạn phát triển tiếp theo:

1. **Mục đích của trang chi tiết Dịch vụ (`/services/{slug}`):** Bạn muốn tạo một trang riêng biệt hiển thị chi tiết từng dịch vụ (như hình ảnh minh họa phong cách, mô tả dài, feedback khách hàng) hay chỉ cần hiển thị bảng giá chung trên trang `/services`?
2. **Cơ chế chia sẻ Hóa đơn cho Khách hàng:** Hóa đơn thanh toán (`/booking/{booking}/invoice`) nên chỉ dành riêng cho Admin in/tải về trong trang quản trị, hay bạn muốn hệ thống sinh link bảo mật riêng (ví dụ kèm mã bảo mật/UUID ngẫu nhiên) để gửi trực tiếp cho khách qua Zalo/SMS?
3. **Môi trường máy chủ dự kiến:** Máy chủ riêng bạn dự định dựng sẽ chạy **Linux (Ubuntu/Debian)** hay **Windows Server**? (Khuyến nghị sử dụng Linux Ubuntu Server để tối ưu hiệu năng và tài nguyên).
4. **Tài khoản TinyPNG & Mail SMTP:** Bạn đã có API Key của TinyPNG và tài khoản SMTP gửi mail (Gmail App Password, Brevo, Resend hoặc Amazon SES) để đưa vào file cấu hình môi trường production chưa?

---
*Tài liệu được khởi tạo tự động phục vụ kế hoạch bảo trì và phát triển dài hạn.*
