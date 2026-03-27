@extends('layouts.app')

@section('title', 'Bộ Sưu Tập | ' . ($settings['site_name'] ?? ''))

@php
    $getImg = fn($key, $default) => empty($settings[$key]) ? $default : (str_starts_with($settings[$key], 'http') ? $settings[$key] : asset('storage/' . $settings[$key]));
@endphp

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <style>
        .masonry-grid { column-count: 1; column-gap: 1.5rem; }
        @media (min-width: 640px) { .masonry-grid { column-count: 2; } }
        @media (min-width: 1024px) { .masonry-grid { column-count: 3; } }
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; transition: all 0.4s ease; }
        .gallery-overlay { background: linear-gradient(to top, rgba(62, 47, 47, 0.9) 0%, rgba(62, 47, 47, 0) 60%); }
    </style>
@endsection

@section('content')
    <section class="relative h-[55vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 w-full h-full">
            <img src="{{ $getImg('portfolio_hero_bg', 'https://images.unsplash.com/photo-1519741497674-611481863552') }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-dark/50"></div>
        </div>
        <div class="relative z-10 text-center text-white px-6" data-aos="fade-up">
            <span class="block text-sm font-light tracking-[0.3em] uppercase mb-4 text-gold">Our Portfolio</span>
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-4">
                {{ $settings['portfolio_title'] ?? 'Dấu Ấn' }} <span class="italic font-light">{{ $settings['portfolio_subtitle'] ?? 'Nghệ Thuật' }}</span>
            </h1>
            <p class="text-lg font-light opacity-80 tracking-widest max-w-2xl mx-auto">
                {{ $settings['portfolio_desc'] ?? 'Khám phá những khoảnh khắc rạng rỡ nhất qua góc nhìn Nude Luxury.' }}
            </p>
        </div>
    </section>

    <section class="py-12 px-6 max-w-7xl mx-auto" data-aos="fade-up">
        <div class="flex flex-wrap justify-center gap-4 md:gap-8 border-b border-dark/10 pb-6">
            <button class="filter-btn active bg-dark text-white px-6 py-2 rounded-full text-sm uppercase shadow-md" data-filter="all">Tất cả</button>
            
            @foreach($portfolioCategories as $category)
                <button class="filter-btn text-dark hover:text-gold hover:bg-white px-6 py-2 rounded-full text-sm uppercase transition-all" data-filter="{{ $category->slug }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </section>

    <section class="pb-32 px-6 max-w-7xl mx-auto min-h-screen">
        <div class="masonry-grid" id="gallery-container">
            @foreach($portfolios as $item)
                {{-- Thay đổi 1: Lấy slug của danh mục làm data-category để phục vụ chức năng lọc ảnh (nếu có) --}}
                <div class="masonry-item opacity-100" data-category="{{ $item->category->slug ?? 'all' }}" data-aos="fade-up">
                    <div class="group block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl bg-gray-100">
                        @if($item->type === 'image')
                            <a href="{{ asset('storage/' . $item->file_path) }}" class="glightbox">
                                <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            </a>
                        @else
                            <video class="w-full object-cover transform group-hover:scale-105 transition-transform duration-700 pointer-events-none" autoplay loop muted playsinline>
                                <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                            </video>
                        @endif

                        <div class="gallery-overlay absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 pointer-events-none">
                            <span class="text-gold text-[10px] tracking-widest uppercase mb-1">
                                {{-- Thay đổi 2: Hiển thị tên danh mục trực tiếp từ Database --}}
                                {{ $item->category->name ?? 'Gallery' }}
                            </span>
                            <h3 class="text-white font-serif text-xl">{{ $item->title ?? ($settings['site_name'] ?? '') }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        const lightbox = GLightbox({ selector: '.glightbox' });
        const filterButtons = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.masonry-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => {
                    btn.classList.remove('bg-dark', 'text-white', 'shadow-md');
                    btn.classList.add('text-dark', 'hover:text-gold', 'hover:bg-white');
                });
                button.classList.remove('text-dark', 'hover:text-gold', 'hover:bg-white');
                button.classList.add('bg-dark', 'text-white', 'shadow-md');

                const filterValue = button.getAttribute('data-filter');
                galleryItems.forEach(item => {
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.style.display = 'block';
                        setTimeout(() => item.classList.remove('opacity-0', 'scale-95'), 50);
                    } else {
                        item.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => item.style.display = 'none', 400);
                    }
                });
            });
        });
    </script>
@endsection