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
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; transition: opacity 0.3s ease, transform 0.3s ease; }
        .gallery-overlay { background: linear-gradient(to top, rgba(62, 47, 47, 0.9) 0%, rgba(62, 47, 47, 0) 60%); }
        
        .filter-btn {
            cursor: pointer;
            transition: all 0.25s ease;
            background-color: transparent;
            color: var(--color-dark, #3e2f2f);
            border: 1px solid rgba(62, 47, 47, 0.15);
            user-select: none;
        }
        .filter-btn:hover {
            color: var(--color-gold, #c8a98d);
            background-color: #ffffff;
            border-color: var(--color-gold, #c8a98d);
        }
        .filter-btn.active {
            background-color: var(--color-dark, #3e2f2f) !important;
            color: #ffffff !important;
            border-color: var(--color-dark, #3e2f2f) !important;
            box-shadow: 0 4px 14px rgba(62, 47, 47, 0.2);
        }
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
        <div class="flex flex-wrap justify-center gap-3 md:gap-4 border-b border-dark/10 pb-6">
            <button type="button" class="filter-btn active px-6 py-2 rounded-full text-sm uppercase font-medium tracking-wide shadow-sm" data-filter="all">Tất cả</button>
            
            @foreach($portfolioCategories as $category)
                <button type="button" class="filter-btn px-6 py-2 rounded-full text-sm uppercase font-medium tracking-wide transition-all" data-filter="{{ $category->slug }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </section>

    <section class="pb-32 px-6 max-w-7xl mx-auto min-h-screen">
        <div class="masonry-grid" id="gallery-container">
            @foreach($portfolios as $item)
                {{-- Lấy slug của danh mục làm data-category để phục vụ chức năng lọc ảnh --}}
                <div class="masonry-item opacity-100" data-category="{{ $item->category->slug ?? 'all' }}" data-aos="fade-up">
                    <div class="group block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl bg-gray-100">
                        @if($item->type === 'image')
                            <a href="{{ asset('storage/' . $item->file_path) }}" class="glightbox">
                                <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            </a>
                        @else
                            <video class="w-full object-cover transform group-hover:scale-105 transition-transform duration-700 pointer-events-none" 
                                   autoplay loop muted playsinline 
                                   poster="{{ !empty($item->poster_path) ? asset('storage/' . $item->poster_path) : '' }}"
                                   preload="metadata">
                                <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                            </video>
                        @endif

                        <div class="gallery-overlay absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 pointer-events-none">
                            <span class="text-gold text-[10px] tracking-widest uppercase mb-1">
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
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let lightbox = null;
            if (typeof GLightbox !== 'undefined') {
                try {
                    lightbox = GLightbox({ selector: '.glightbox' });
                } catch (e) {
                    console.warn('GLightbox init error:', e);
                }
            }

            const filterButtons = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.masonry-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filterValue = this.getAttribute('data-filter');

                    galleryItems.forEach(item => {
                        const itemCategory = item.getAttribute('data-category');
                        if (filterValue === 'all' || itemCategory === filterValue) {
                            item.style.display = 'block';
                            requestAnimationFrame(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'scale(1)';
                            });
                        } else {
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                if (item.style.opacity === '0') {
                                    item.style.display = 'none';
                                }
                            }, 250);
                        }
                    });

                    if (lightbox && typeof lightbox.reload === 'function') {
                        lightbox.reload();
                    }
                    if (window.AOS && typeof window.AOS.refresh === 'function') {
                        window.AOS.refresh();
                    }
                });
            });
        });
    </script>
@endsection