@extends('layouts.app')

@section('title', 'Giới Thiệu | Luyện Thị Thảo Makeup Artist')

@php
    $getImg = fn($key, $default) => empty($settings[$key]) ? $default : (str_starts_with($settings[$key], 'http') ? $settings[$key] : asset('storage/' . $settings[$key]));
@endphp

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <style>
        .about-image-shadow { box-shadow: 20px 20px 0px 0px #c8a98d; }
        .play-btn-pulse { animation: pulse-ring 2s infinite; }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(200, 169, 141, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(200, 169, 141, 0); }
            100% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(200, 169, 141, 0); }
        }
    </style>
@endsection

@section('content')
    <section class="relative h-[55vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 w-full h-full">
            <img src="{{ $getImg('about_hero_bg', 'https://images.unsplash.com/photo-1519741497674-611481863552') }}" alt="Hero About" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-dark/50"></div>
        </div>
        <div class="relative z-10 text-center text-white px-6" data-aos="fade-up">
            <span class="block text-sm font-light tracking-[0.3em] uppercase mb-4 text-gold">The Story</span>
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-4 italic">{{ $settings['about_hero_title'] ?? 'Hành Trình Nghệ Thuật' }}</h1>
            <p class="text-lg font-light opacity-80 tracking-widest">{{ $settings['about_hero_desc'] ?? 'Tôn vinh vẻ đẹp nguyên bản' }}</p>
        </div>
    </section>

    <section id="about" class="py-32 px-6 bg-primary">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
            <div class="w-full md:w-5/12" data-aos="fade-right">
                <div class="relative about-image-shadow rounded-2xl overflow-hidden">
                    <img src="{{ $getImg('about_me_image', 'https://images.unsplash.com/photo-1595959183082-7b570b7e08e2') }}" class="w-full aspect-[4/5] object-cover">
                </div>
            </div>
            <div class="w-full md:w-7/12" data-aos="fade-left">
                <span class="text-gold text-xs tracking-[0.4em] uppercase mb-4 block font-bold">Makeup Artist</span>
                <h2 class="text-4xl md:text-5xl font-serif text-dark mb-8 leading-tight">{!! $settings['about_me_title'] ?? 'Xin chào, tôi là <br><span class="italic">Luyện Thị Thảo</span>' !!}</h2>
                <div class="space-y-6 text-gray-700 leading-relaxed font-light text-lg">
                    {!! nl2br(e($settings['about_me_content'] ?? "Hành trình đến với nghệ thuật trang điểm của tôi bắt nguồn từ niềm đam mê mãnh liệt với cái đẹp và khát khao đánh thức sự tự tin trong mỗi người phụ nữ.\n\nHoạt động chuyên nghiệp tại Hà Nội và Hưng Yên, tôi theo đuổi phong cách Nude Luxury – tinh giản, sang trọng nhưng đầy chiều sâu.")) !!}
                </div>
                <img src="{{ $getImg('about_me_signature', 'https://images.unsplash.com/photo-1596462502278-27bfdc403348') }}" class="w-32 mt-10 opacity-60 mix-blend-multiply">
            </div>
        </div>
    </section>

    <section class="py-24 bg-white px-6">
        <div class="max-w-4xl mx-auto text-center" data-aos="zoom-in">
            <span class="text-gold text-xs tracking-widest uppercase mb-4 block">The Inspiration</span>
            <h2 class="text-3xl font-serif text-dark mb-12">Thước Phim Cảm Hứng</h2>
            <div class="relative rounded-3xl overflow-hidden group shadow-2xl aspect-video cursor-pointer border-8 border-primary">
                <img src="{{ $getImg('about_video_cover', 'https://images.unsplash.com/photo-1522337660859-02fbefca4702') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/40 transition-colors"></div>
                <a href="{{ $settings['about_video_link'] ?? 'https://www.youtube.com/watch?v=maSuEtbPJ8Y' }}" class="glightbox absolute inset-0 flex items-center justify-center z-10">
                    <div class="w-20 h-20 bg-white/90 rounded-full flex items-center justify-center play-btn-pulse text-gold">
                        <i class="fas fa-play text-2xl ml-1"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="py-20 bg-primary border-y border-gold/10">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            <div data-aos="fade-up" data-aos-delay="0">
                <div class="text-5xl font-serif text-gold font-bold mb-2">
                    <span class="purecounter" data-purecounter-end="{{ $settings['count_brides'] ?? '850' }}">0</span>+
                </div>
                <p class="text-dark/60 uppercase tracking-widest text-[10px] font-bold">Cô dâu tin tưởng</p>
            </div>
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="text-5xl font-serif text-gold font-bold mb-2">
                    <span class="purecounter" data-purecounter-end="{{ $settings['count_events'] ?? '320' }}">0</span>+
                </div>
                <p class="text-dark/60 uppercase tracking-widest text-[10px] font-bold">Sự kiện & Nghệ sĩ</p>
            </div>
            <div data-aos="fade-up" data-aos-delay="200">
                <div class="text-5xl font-serif text-gold font-bold mb-2">
                    <span class="purecounter" data-purecounter-end="{{ $settings['count_years'] ?? '7' }}">0</span>+
                </div>
                <p class="text-dark/60 uppercase tracking-widest text-[10px] font-bold">Năm kinh nghiệm</p>
            </div>
        </div>
    </section>

    <section class="py-32 bg-white px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-serif text-dark italic">Hành Trình Chinh Phục</h2>
            </div>
            <div class="space-y-16 relative before:absolute before:inset-0 before:left-0 md:before:left-1/2 before:w-[1px] before:bg-gold/20">
                <div class="relative flex flex-col md:flex-row items-center" data-aos="fade-up">
                    <div class="md:w-1/2 md:pr-12 md:text-right">
                        <h4 class="text-2xl font-serif text-gold mb-2">{{ $settings['timeline_1_year'] ?? '2019' }}</h4>
                        <p class="font-light text-gray-600 text-sm">{{ $settings['timeline_1_text'] ?? 'Bắt đầu hành trình với các khóa đào tạo chuyên sâu tại Hà Nội.' }}</p>
                    </div>
                    <div class="w-3 h-3 bg-gold rounded-full absolute left-[-6px] md:left-1/2 md:-translate-x-1/2 z-10 shadow-[0_0_0_6px_rgba(200,169,141,0.2)]"></div>
                    <div class="md:w-1/2"></div>
                </div>
                <div class="relative flex flex-col md:flex-row items-center" data-aos="fade-up">
                    <div class="md:w-1/2"></div>
                    <div class="w-3 h-3 bg-gold rounded-full absolute left-[-6px] md:left-1/2 md:-translate-x-1/2 z-10 shadow-[0_0_0_6px_rgba(200,169,141,0.2)]"></div>
                    <div class="md:w-1/2 md:pl-12">
                        <h4 class="text-2xl font-serif text-gold mb-2">{{ $settings['timeline_2_year'] ?? '2021' }}</h4>
                        <p class="font-light text-gray-600 text-sm">{{ $settings['timeline_2_text'] ?? 'Định hình phong cách Nude Luxury và phục vụ hàng trăm cô dâu.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 bg-primary px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-serif text-dark">Dấu Ấn Làm Đẹp</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="overflow-hidden rounded-2xl shadow-lg group" data-aos="fade-up">
                    <div class="overflow-hidden rounded-2xl shadow-lg group"><img src="{{ $getImg('about_gallery_1', 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1') }}" class="w-full h-[500px] object-cover group-hover:scale-110 transition-transform duration-700"></div>
                </div>
                <div class="overflow-hidden rounded-2xl shadow-lg group" data-aos="fade-up" data-aos-delay="100">
                    <div class="overflow-hidden rounded-2xl shadow-lg group"><img src="{{ $getImg('about_gallery_2', 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e') }}" class="w-full h-[500px] object-cover group-hover:scale-110 transition-transform duration-700"></div>
                </div>
                <div class="overflow-hidden rounded-2xl shadow-lg group" data-aos="fade-up" data-aos-delay="200">
                    
            <div class="overflow-hidden rounded-2xl shadow-lg group"><img src="{{ $getImg('about_gallery_3', 'https://images.unsplash.com/photo-1492707892479-7bc8d5a4ee93') }}" class="w-full h-[500px] object-cover group-hover:scale-110 transition-transform duration-700"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="academy" class="relative py-32 text-white text-center overflow-hidden">
        <div class="absolute inset-0 w-full h-full">
            <img src="{{ $getImg('about_academy_bg', 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937') }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-[#2a1f1f]/80"></div> </div>

        <div class="relative z-10 max-w-3xl mx-auto px-6" data-aos="zoom-in">
            <span class="text-gold text-xs tracking-widest uppercase mb-4 block" style="color: #c8a98d;">Academy</span>
            <h2 class="text-4xl md:text-5xl font-serif mb-6 italic">Đào Tạo Chuyên Nghiệp</h2>
            <p class="text-lg font-light text-white/80 mb-10 leading-relaxed">
                {{ $settings['about_academy_desc'] ?? 'Khơi dậy tiềm năng nghệ thuật trong bạn. Các khóa đào tạo từ makeup cá nhân đến chuyên nghiệp, cam kết truyền lửa và đồng hành cùng đam mê.' }}
            </p>
            <a href="{{ $settings['social_zalo'] ?? '#' }}" target="_blank" class="inline-block px-10 py-4 rounded-full text-xs uppercase tracking-widest font-bold transition-all" style="background-color: #c8a98d; color: #3e2f2f;">
                Tìm hiểu khóa học
            </a>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@srexi/purecounterjs/dist/purecounter_vanilla.js"></script>
    <script>
        const lightbox = GLightbox({ selector: '.glightbox' });
        new PureCounter();
    </script>
@endsection