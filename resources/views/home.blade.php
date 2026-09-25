@extends('layouts.app')

@section('title', $settings['site_name'] ?? 'Trang Chủ')

@php
    $getImg = fn($key, $default) => empty($settings[$key]) ? $default : (str_starts_with($settings[$key], 'http') ? $settings[$key] : asset('storage/' . $settings[$key]));
@endphp

@section('content')

    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        @if($banners->count() > 0)
            @foreach($banners as $banner)
                <div class="absolute inset-0 w-full h-full">
                    <img src="{{ asset('storage/' . $banner->image_path) }}" 
                        alt="{{ $banner->title }}" 
                        class="w-full h-full object-cover scale-105 animate-[pulse_20s_ease-in-out_infinite_alternate]">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>
                <div class="relative z-10 text-center text-white px-6 max-w-4xl" data-aos="fade-up">
                    <span class="block text-sm font-light tracking-[0.3em] uppercase mb-4 text-gold">
                        {{ $settings['home_hero_subtitle'] ?? 'High-End Experience' }}
                    </span>
                    <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 leading-tight">{{ $banner->title }}</h1>
                    <a href="{{ url('/booking') }}" class="inline-block btn-theme-primary px-10 py-4 rounded-full text-sm uppercase tracking-wider hover:opacity-90 transition-all duration-500 shadow-xl">
                        Đặt lịch ngay
                    </a>
                </div>
            @endforeach
        @else
            {{-- Phần hiển thị mặc định khi không có dữ liệu Banner --}}
            <div class="absolute inset-0 w-full h-full">
                <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            <div class="relative z-10 text-center text-white px-6 max-w-4xl" data-aos="fade-up">
                <span class="block text-sm font-light tracking-[0.3em] uppercase mb-4 text-gold">
                    {{ $settings['home_hero_subtitle'] ?? 'High-End Experience' }}
                </span>
                {{-- Thay thế tên cứng bằng site_name --}}
                <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 leading-tight">
                    {{ $settings['site_name'] ?? '' }}
                </h1>
                <a href="{{ url('/booking') }}" class="inline-block btn-theme-primary px-10 py-4 rounded-full text-sm uppercase tracking-wider hover:opacity-90 transition-all duration-500 shadow-xl">
                    Đặt lịch ngay
                </a>
            </div>
        @endif
    </section>

    <section class="py-32 px-6 max-w-4xl mx-auto text-center" data-aos="fade-up">
        <h2 class="text-4xl md:text-5xl font-serif mb-8 text-dark">{{ $settings['home_intro_title'] ?? 'Nghệ Thuật Của Sự Tinh Tế' }}</h2>
        <p class="text-lg font-light leading-relaxed text-gray-700 italic">
            "{{ $settings['home_slogan'] ?? 'Mỗi khuôn mặt là một tác phẩm nghệ thuật riêng biệt. Sứ mệnh của tôi là đánh thức vẻ đẹp rạng rỡ nhất ẩn sâu bên trong bạn.' }}"
        </p>
    </section>

    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="text-gold text-sm tracking-widest uppercase mb-2 block font-medium">{{ $settings['home_services_subtitle'] ?? 'Bảng giá & Dịch vụ' }}</span>
                <h2 class="text-4xl md:text-5xl font-serif text-dark uppercase tracking-tight">{{ $settings['home_services_title'] ?? 'Luxury Services' }}</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($services as $service)
                <div class="group bg-primary p-12 rounded-2xl text-center hover:bg-dark hover:text-white transition-all duration-500 shadow-sm shadow-black/5 transform hover:-translate-y-2">
                    <h3 class="text-2xl font-serif mb-4 uppercase tracking-wider">{{ $service->name }}</h3>
                    <div class="w-12 h-[1px] bg-gold mx-auto mb-6 group-hover:w-20 transition-all"></div>
                    <p class="font-bold text-gold mb-4 text-xl tracking-widest">{{ $service->price_text }}</p>
                    <div class="text-sm font-light opacity-80 leading-relaxed">
                        @php 
                            // Nếu là chuỗi thì giải mã, nếu đã là mảng thì giữ nguyên
                            $features = is_string($service->features) ? json_decode($service->features, true) : $service->features; 
                        @endphp
                        
                        @if(!empty($features) && is_array($features))
                            <ul class="space-y-2">
                                @foreach($features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-32 bg-primary">
        <div class="max-w-5xl mx-auto px-6" data-aos="fade-up">
            <div class="text-center mb-16">
                <span class="text-xs uppercase tracking-[0.3em] text-gold font-semibold mb-3 block">Biến Hóa Diện Mạo</span>
                <h2 class="text-4xl md:text-5xl font-serif text-dark tracking-tight">{{ $settings['home_transform_title'] ?? 'The Magic Transformation' }}</h2>
                <div class="w-16 h-[2px] bg-gold mx-auto mt-6"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 max-w-4xl mx-auto items-stretch">
                <!-- Before Card -->
                <div class="group relative rounded-3xl overflow-hidden shadow-2xl border-4 md:border-8 border-white bg-dark/5 aspect-[9/16] transition-all duration-500 hover:shadow-black/10">
                    <img src="{{ $getImg('home_transform_before', 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=2000') }}" 
                         alt="Before Transformation" 
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" />
                    
                    <!-- Gradient Overlays for readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/80 via-transparent to-dark/30 pointer-events-none"></div>

                    <!-- Top Badge -->
                    <div class="absolute top-6 left-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-serif uppercase tracking-widest bg-dark/70 text-white/90 backdrop-blur-md border border-white/20 shadow-sm">
                            Before
                        </span>
                    </div>

                    <!-- Bottom Info -->
                    <div class="absolute bottom-6 left-6 right-6 text-left">
                        <p class="text-white/70 text-xs uppercase tracking-widest font-sans mb-1">Khoảnh khắc ban đầu</p>
                        <h3 class="text-white text-xl md:text-2xl font-serif font-medium tracking-wide">Vẻ Đẹp Mộc Mạc</h3>
                    </div>
                </div>

                <!-- After Card -->
                <div class="group relative rounded-3xl overflow-hidden shadow-2xl border-4 md:border-8 border-white bg-dark/5 aspect-[9/16] transition-all duration-500 hover:shadow-black/10">
                    <img src="{{ $getImg('home_transform_after', 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=2000') }}" 
                         alt="After Transformation" 
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" />
                    
                    <!-- Gradient Overlays for readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/80 via-transparent to-dark/30 pointer-events-none"></div>

                    <!-- Top Badge -->
                    <div class="absolute top-6 left-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-serif uppercase tracking-widest bg-gold text-white backdrop-blur-md border border-white/30 shadow-md">
                            ★ After
                        </span>
                    </div>

                    <!-- Bottom Info -->
                    <div class="absolute bottom-6 left-6 right-6 text-left">
                        <p class="text-gold text-xs uppercase tracking-widest font-sans mb-1">Sau khi trang điểm</p>
                        <h3 class="text-white text-xl md:text-2xl font-serif font-medium tracking-wide">Nghệ Thuật Biến Hóa</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-end mb-16 border-b border-black/5 pb-8">
                <h2 class="text-4xl font-serif italic text-dark">{{ $settings['home_journal_title'] ?? 'Journal' }}</h2>
                <a href="{{ url('/posts') }}" class="text-xs uppercase tracking-[0.3em] font-medium text-gold hover:text-dark transition-colors">Xem tất cả →</a>
            </div>
            <div class="grid md:grid-cols-3 gap-12">
                @foreach($latestPosts as $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="group block overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="aspect-[4/5] overflow-hidden mb-6 rounded-lg">
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-700">
                    </div>
                    <span class="text-[10px] uppercase tracking-widest text-gold mb-2 block font-bold">{{ $post->published_at ? $post->published_at->format('d/m/Y') : $post->created_at->format('d/m/Y') }}</span>
                    <h3 class="text-xl font-serif text-dark group-hover:text-gold transition-colors leading-snug">{{ $post->title }}</h3>
                </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection