<header id="navbar" class="fixed w-full z-50 transition-all duration-500 bg-transparent py-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 md:px-12">
        <a href="/" class="text-2xl font-serif font-bold text-dark tracking-wide hover:opacity-80 transition-opacity flex items-center gap-2">
            @if(!empty($settings['site_logo']))
                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" class="h-9 w-auto object-contain">
            @else
                {{ $settings['site_name'] ?? 'Makeup Artist' }}
            @endif
        </a>

        <nav class="hidden md:flex gap-10 text-sm font-medium tracking-wide">
            <a href="/" class="{{ Request::is('/') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Trang chủ</a>
            <a href="/gioi-thieu" class="{{ Request::is('gioi-thieu') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Giới thiệu</a>
            <a href="/portfolio" class="{{ Request::is('portfolio*') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Bộ sưu tập</a>
            <a href="/services" class="{{ Request::is('services*') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Dịch vụ</a>
            <a href="/posts" class="{{ Request::is('posts*') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Tạp chí</a>
        </nav>

        <a href="/booking" class="hidden md:inline-block bg-gold text-white px-6 py-2.5 rounded-full text-sm hover:bg-[#b5977d] transition-colors duration-300 shadow-lg">
            Đặt lịch ngay
        </a>

        <button onclick="toggleMobileMenu()" class="md:hidden text-dark p-2 focus:outline-none">
            <i class="fa-solid fa-bars-staggered text-2xl"></i>
        </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-md shadow-2xl border-t border-gray-100 py-6 px-6">
        <div class="flex flex-col gap-6 text-center">
            <a href="/" class="{{ Request::is('/') ? 'text-gold' : 'text-dark' }} text-base font-medium uppercase tracking-widest">Trang chủ</a>
            <a href="/gioi-thieu" class="{{ Request::is('gioi-thieu') ? 'text-gold' : 'text-dark' }} text-base font-medium uppercase tracking-widest">Giới thiệu</a>
            <a href="/portfolio" class="{{ Request::is('portfolio*') ? 'text-gold' : 'text-dark' }} text-base font-medium uppercase tracking-widest">Bộ sưu tập</a>
            <a href="/services" class="{{ Request::is('services*') ? 'text-gold' : 'text-dark' }} text-base font-medium uppercase tracking-widest">Dịch vụ</a>
            <a href="/posts" class="{{ Request::is('posts*') ? 'text-gold' : 'text-dark' }} text-base font-medium uppercase tracking-widest">Tạp chí</a>
            
            <hr class="border-gray-100">
            
            <a href="/booking" class="bg-gold text-white px-8 py-3 rounded-full text-sm font-bold uppercase tracking-widest shadow-md">
                Đặt lịch ngay
            </a>
        </div>
    </div>
</header>