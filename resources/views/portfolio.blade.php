@extends('layouts.app')

@section('title', 'Bộ Sưu Tập | Luyện Thị Thảo Makeup Artist')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <style>
        /* Cấu trúc Masonry Layout chia cột tự động */
        .masonry-grid { column-count: 1; column-gap: 1.5rem; }
        @media (min-width: 640px) { .masonry-grid { column-count: 2; } }
        @media (min-width: 1024px) { .masonry-grid { column-count: 3; } }
        
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; transition: all 0.4s ease; }
        
        /* Hiệu ứng bóng đen phủ lên khi di chuột */
        .gallery-overlay { background: linear-gradient(to top, rgba(62, 47, 47, 0.9) 0%, rgba(62, 47, 47, 0) 60%); }
    </style>
@endsection

@section('content')
    <section class="pt-40 pb-12 px-6 max-w-7xl mx-auto text-center" data-aos="fade-up">
        <span class="text-gold text-sm tracking-widest uppercase mb-4 block">Our Portfolio</span>
        <h1 class="text-5xl md:text-7xl font-serif text-dark leading-tight mb-6">Dấu Ấn <span class="italic font-light">Nghệ Thuật</span></h1>
        <p class="font-light text-gray-600 max-w-2xl mx-auto text-lg">Khám phá những khoảnh khắc rạng rỡ nhất qua góc nhìn Nude Luxury.</p>
    </section>

    <section class="pb-8 px-6 max-w-7xl mx-auto" data-aos="fade-up">
        <div class="flex flex-wrap justify-center gap-4 md:gap-8 border-b border-dark/10 pb-6">
            <button class="filter-btn active bg-dark text-white px-6 py-2 rounded-full text-sm uppercase shadow-md" data-filter="all">Tất cả</button>
            <button class="filter-btn text-dark hover:text-gold hover:bg-white px-6 py-2 rounded-full text-sm uppercase transition-all" data-filter="bride">Cô Dâu</button>
            <button class="filter-btn text-dark hover:text-gold hover:bg-white px-6 py-2 rounded-full text-sm uppercase transition-all" data-filter="event">Sự Kiện</button>
            <button class="filter-btn text-dark hover:text-gold hover:bg-white px-6 py-2 rounded-full text-sm uppercase transition-all" data-filter="personal">Cá Nhân / Kỷ Yếu</button>
        </div>
    </section>

    <section class="pb-32 px-6 max-w-7xl mx-auto min-h-screen">
        <div class="masonry-grid" id="gallery-container">
            
            @foreach($portfolios as $item)
                <div class="masonry-item opacity-100" data-category="{{ $item->category }}" data-aos="fade-up">
                    <div class="group block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl bg-gray-100">
                        
                        @if($item->type === 'image')
                            <a href="{{ asset('storage/' . $item->file_path) }}" class="glightbox">
                                <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            </a>
                        @else
                            <video class="w-full object-cover transform group-hover:scale-105 transition-transform duration-700 pointer-events-none" 
                                   autoplay loop muted playsinline>
                                <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                            </video>
                        @endif

                        <div class="gallery-overlay absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 pointer-events-none">
                            <span class="text-gold text-[10px] tracking-widest uppercase mb-1">
                                {{ match($item->category) { 'bride' => 'Wedding', 'event' => 'Event', 'personal' => 'Portrait', default => 'Gallery' } }}
                            </span>
                            <h3 class="text-white font-serif text-xl">{{ $item->title ?? 'Luyện Thị Thảo' }}</h3>
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
        // Kích hoạt tính năng phóng to ảnh
        const lightbox = GLightbox({ selector: '.glightbox' });

        // Kích hoạt bộ lọc danh mục
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