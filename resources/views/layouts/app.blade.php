<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Luyện Thị Thảo | Makeup Artist Chuyên Nghiệp')</title>
    
    <meta name="description" content="Luyện Thị Thảo - Makeup Artist chuyên trang điểm cô dâu, sự kiện, nghệ sĩ tại Hà Nội và Hưng Yên.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/img-comparison-slider@8/dist/styles.css"/>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f6f1ec', // Màu nền kem sang trọng
                        gold: '#c8a98d',    // Màu vàng đồng
                        dark: '#3e2f2f',    // Màu nâu trầm trong ảnh mẫu
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* Tùy chỉnh thanh cuộn */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c8a98d; }
        
        .bg-gold { background-color: #c8a98d; }
        .text-gold { color: #c8a98d; }
        .border-gold { border-color: #c8a98d; }
    </style>
    @yield('styles')
</head>
<body class="bg-primary text-dark antialiased">

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/img-comparison-slider@8/dist/index.js"></script>
    
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
</body>
</html>