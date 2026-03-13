<header id="navbar" class="fixed w-full z-50 transition-all duration-500 bg-transparent py-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 md:px-12">
        <a href="{{ url('/') }}" class="text-2xl font-serif font-bold text-dark tracking-wide hover:opacity-80 transition-opacity">
            {{ $settings['site_name'] ?? 'Luyện Thị Thảo' }}
        </a>

        <nav class="hidden md:flex gap-10 text-sm font-medium tracking-wide">
            <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Trang chủ</a>
            <a href="{{ url('/gioi-thieu') }}" class="{{ Request::is('gioi-thieu') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Giới thiệu</a>
            <a href="{{ url('/portfolio') }}" class="{{ Request::is('portfolio*') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Bộ sưu tập</a>
            <a href="{{ url('/services') }}" class="{{ Request::is('services*') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Dịch vụ</a>
            <a href="{{ url('/posts') }}" class="{{ Request::is('posts*') ? 'text-gold' : 'text-dark hover:text-gold' }} transition-colors duration-300">Tạp chí</a>
        </nav>

        <a href="{{ url('/booking') }}" class="hidden md:inline-block bg-gold text-white px-6 py-2.5 rounded-full text-sm hover:bg-[#b5977d] transition-colors duration-300 shadow-lg">
            Đặt lịch ngay
        </a>
    </div>
</header>