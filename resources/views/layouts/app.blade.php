<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings['site_name'] ?? 'Makeup Artist & Beauty Studio')</title>
    <link rel="canonical" href="{{ url()->current() }}">
    
    <meta name="description" content="@yield('meta_description', $settings['site_description'] ?? 'Dịch vụ trang điểm chuyên nghiệp, tôn vinh vẻ đẹp tự nhiên và sang trọng.')">
    @if(!empty($settings['site_keywords']))
        <meta name="keywords" content="{{ $settings['site_keywords'] }}">
    @endif
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    
    @hasSection('meta')
        @yield('meta')
    @else
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $settings['site_name'] ?? 'Makeup Artist' }}">
        <meta property="og:description" content="{{ $settings['site_description'] ?? 'Dịch vụ trang điểm chuyên nghiệp.' }}">
        <meta property="og:image" content="{{ !empty($settings['site_meta_image']) ? (str_starts_with($settings['site_meta_image'], 'http') ? $settings['site_meta_image'] : asset('storage/' . $settings['site_meta_image'])) : asset('images/default-share.jpg') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $settings['site_name'] ?? 'Makeup Artist' }}">
        <meta name="twitter:description" content="{{ $settings['site_description'] ?? 'Dịch vụ trang điểm chuyên nghiệp.' }}">
        <meta name="twitter:image" content="{{ !empty($settings['site_meta_image']) ? (str_starts_with($settings['site_meta_image'], 'http') ? $settings['site_meta_image'] : asset('storage/' . $settings['site_meta_image'])) : asset('images/default-share.jpg') }}">
    @endif

    @if(!empty($settings['site_favicon']))
        <link rel="icon" href="{{ asset('storage/' . $settings['site_favicon']) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . $settings['site_favicon']) }}">
    @endif

    {{-- JSON-LD Structured Data (Schema.org / BeautySalon) --}}
    @php
        $sameAs = array_values(array_filter([
            $settings['social_facebook'] ?? null,
            $settings['social_instagram'] ?? null,
            $settings['social_tiktok'] ?? null,
            $settings['social_youtube'] ?? null,
            $settings['social_zalo'] ?? null,
        ]));
        $logoUrl = !empty($settings['site_logo']) ? (str_starts_with($settings['site_logo'], 'http') ? $settings['site_logo'] : asset('storage/' . $settings['site_logo'])) : asset('favicon.ico');
        $schemaBeautySalon = [
            '@context' => 'https://schema.org',
            '@type' => 'BeautySalon',
            'name' => $settings['site_name'] ?? 'Makeup Artist Studio',
            'description' => $settings['site_description'] ?? 'Dịch vụ trang điểm chuyên nghiệp.',
            'url' => url('/'),
            'logo' => $logoUrl,
            'image' => !empty($settings['site_meta_image']) ? asset('storage/' . $settings['site_meta_image']) : $logoUrl,
            'telephone' => $settings['hotline'] ?? '',
            'email' => $settings['email'] ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings['address_main'] ?? ($settings['address'] ?? 'Hà Nội'),
                'addressLocality' => 'Hà Nội',
                'addressCountry' => 'VN',
            ],
            'priceRange' => '$$',
            'openingHours' => 'Mo-Su 08:00-20:00',
            'sameAs' => $sameAs,
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($schemaBeautySalon, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    @php
        $headingFontType = $settings['theme_font_heading_type'] ?? 'google';
        $headingFont = $settings['theme_font_heading'] ?? 'Playfair Display';
        $customHeadingFont = $settings['theme_custom_heading_font_file'] ?? null;

        $bodyFontType = $settings['theme_font_body_type'] ?? 'google';
        $bodyFont = $settings['theme_font_body'] ?? 'Inter';
        $customBodyFont = $settings['theme_custom_body_font_file'] ?? null;

        $googleFonts = [];
        if ($headingFontType === 'google' && !empty($headingFont)) {
            $googleFonts[] = 'family=' . str_replace(' ', '+', $headingFont) . ':ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600';
        }
        if ($bodyFontType === 'google' && !empty($bodyFont)) {
            $googleFonts[] = 'family=' . str_replace(' ', '+', $bodyFont) . ':ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600';
        }

        $googleFontsUrl = !empty($googleFonts) ? 'https://fonts.googleapis.com/css2?' . implode('&', array_unique($googleFonts)) . '&display=swap' : null;

        $colorPrimary = $settings['theme_color_primary'] ?? '#f6f1ec';
        $colorGold = $settings['theme_color_gold'] ?? '#c8a98d';
        $colorDark = $settings['theme_color_dark'] ?? '#3e2f2f';
        $colorButton = $settings['theme_color_button'] ?? '#3e2f2f';
        $colorButtonText = $settings['theme_color_button_text'] ?? '#ffffff';

        $headingFontFamily = ($headingFontType === 'custom' && !empty($customHeadingFont)) ? "'CustomHeadingFont', serif" : "'{$headingFont}', serif";
        $bodyFontFamily = ($bodyFontType === 'custom' && !empty($customBodyFont)) ? "'CustomBodyFont', sans-serif" : "'{$bodyFont}', sans-serif";
    @endphp

    @if($googleFontsUrl)
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $googleFontsUrl }}" rel="stylesheet">
    @endif

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style id="theme-custom-properties">
        @if($headingFontType === 'custom' && !empty($customHeadingFont))
        @font-face {
            font-family: 'CustomHeadingFont';
            src: url('{{ asset('storage/' . $customHeadingFont) }}');
            font-weight: 100 900;
            font-display: swap;
        }
        @endif

        @if($bodyFontType === 'custom' && !empty($customBodyFont))
        @font-face {
            font-family: 'CustomBodyFont';
            src: url('{{ asset('storage/' . $customBodyFont) }}');
            font-weight: 100 900;
            font-display: swap;
        }
        @endif

        :root {
            --color-primary: {{ $colorPrimary }};
            --color-gold: {{ $colorGold }};
            --color-dark: {{ $colorDark }};
            --color-button: {{ $colorButton }};
            --color-button-text: {{ $colorButtonText }};
            --font-serif: {!! $headingFontFamily !!};
            --font-sans: {!! $bodyFontFamily !!};
        }

        ::-webkit-scrollbar-thumb { background: {{ $colorGold }}; }
        .bg-gold { background-color: {{ $colorGold }}; }
        .text-gold { color: {{ $colorGold }}; }
        .border-gold { border-color: {{ $colorGold }}; }

        .btn-theme-primary {
            background-color: {{ $colorButton }};
            color: {{ $colorButtonText }};
        }
    </style>

    @if(!empty($settings['theme_custom_css']))
    <style id="theme-custom-injected-css">
        {!! $settings['theme_custom_css'] !!}
    </style>
    @endif

    @yield('styles')
</head>
<body class="bg-primary text-dark antialiased">

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        // Khởi tạo hiệu ứng xuất hiện AOS
        AOS.init({
            duration: 1000,
            offset: 100,
            once: true
        });

        // Hiệu ứng Header đổi màu khi cuộn (Từ trang Blog & Index)
        window.addEventListener('scroll', () => {
            const header = document.getElementById('navbar');
            if (window.scrollY > 50) {
                header.classList.remove('bg-transparent', 'py-4');
                header.classList.add('bg-primary/95', 'py-3', 'shadow-sm', 'backdrop-blur-md');
            } else {
                header.classList.add('bg-transparent', 'py-4');
                header.classList.remove('bg-primary/95', 'py-3', 'shadow-sm', 'backdrop-blur-md');
            }
        });

        // Xử lý Mobile Menu (Nếu bạn thêm nút Menu mobile sau này)
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
    @yield('scripts')
    @if(isset($settings['popup_active']) && $settings['popup_active'] == '1')
    <div id="welcome-popup" class="fixed inset-0 z-[100] items-center justify-center bg-black/70 hidden opacity-0 transition-opacity duration-500">
        <div class="bg-white rounded-2xl overflow-hidden max-w-lg w-full mx-4 relative transform scale-95 transition-transform duration-500" id="popup-content">
            <button onclick="closePopup()" class="absolute top-4 right-4 w-8 h-8 bg-black/20 hover:bg-black/40 rounded-full flex items-center justify-center text-white z-10 transition-colors">
                <i class="fas fa-times"></i>
            </button>
            
            @php 
                $popupImg = $settings['popup_image'] ?? 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1';
                $popupSrc = str_starts_with($popupImg, 'http') ? $popupImg : asset('storage/' . $popupImg);
            @endphp
            <img src="{{ $popupSrc }}" class="w-full h-64 object-cover">

            <div class="p-8 text-center">
                <h3 class="text-3xl font-serif text-dark mb-3">{{ $settings['popup_title'] ?? 'Thông báo' }}</h3>
                <p class="text-gray-600 font-light mb-6">{{ $settings['popup_desc'] ?? '' }}</p>
                @if(!empty($settings['popup_link']))
                <a href="{{ $settings['popup_link'] }}" class="inline-block bg-[#c8a98d] text-white px-8 py-3 rounded-full text-sm uppercase tracking-widest hover:bg-[#3e2f2f] transition-colors">
                    Xem chi tiết
                </a>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!sessionStorage.getItem('popup_closed')) {
                const popup = document.getElementById('welcome-popup');
                const content = document.getElementById('popup-content');
                setTimeout(() => {
                    popup.classList.remove('hidden');
                    popup.classList.add('flex');
                    setTimeout(() => { 
                        popup.classList.remove('opacity-0'); 
                        content.classList.remove('scale-95'); 
                    }, 50);
                }, 1000);
            }
        });
        function closePopup() {
            const popup = document.getElementById('welcome-popup');
            const content = document.getElementById('popup-content');
            popup.classList.add('opacity-0'); 
            content.classList.add('scale-95');
            setTimeout(() => { 
                popup.classList.add('hidden'); 
                popup.classList.remove('flex'); // Xóa flex khi ẩn đi để tránh xung đột
                sessionStorage.setItem('popup_closed', 'true'); 
            }, 500);
        }
    </script>
@endif
</body>
</html>