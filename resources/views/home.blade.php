@extends('layouts.app')

@section('title', 'Luyện Thị Thảo | Makeup Artist Chuyên Nghiệp')

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
                    <a href="{{ url('/booking') }}" class="inline-block bg-gold text-white px-10 py-4 rounded-full text-sm uppercase tracking-wider hover:bg-white hover:text-dark transition-all duration-500">
                        Đặt lịch ngay
                    </a>
                </div>
            @endforeach
        @else
            <div class="absolute inset-0 w-full h-full">
                <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            <div class="relative z-10 text-center text-white px-6 max-w-4xl" data-aos="fade-up">
                <span class="block text-sm font-light tracking-[0.3em] uppercase mb-4 text-gold">{{ $settings['home_hero_subtitle'] ?? 'High-End Experience' }}</span>
                <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 leading-tight">Luyện Thị Thảo <br> Makeup Artist</h1>
                <a href="{{ url('/booking') }}" class="inline-block bg-gold text-white px-10 py-4 rounded-full text-sm uppercase tracking-wider hover:bg-white hover:text-dark transition-all duration-500">Đặt lịch ngay</a>
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
                <div class="group bg-primary p-12 rounded-2xl text-center hover:bg-[#3e2f2f] hover:text-white transition-all duration-500 shadow-sm shadow-black/5 transform hover:-translate-y-2">
                    <h3 class="text-2xl font-serif mb-4 uppercase tracking-wider">{{ $service->name }}</h3>
                    <div class="w-12 h-[1px] bg-gold mx-auto mb-6 group-hover:w-20 transition-all"></div>
                    <p class="font-bold text-gold mb-4 text-xl tracking-widest">{{ $service->price_text }}</p>
                    <div class="text-sm font-light opacity-80 leading-relaxed">
                        @php $features = json_decode($service->features); @endphp
                        @if($features)
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
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-serif mb-12">{{ $settings['home_transform_title'] ?? 'The Magic Transformation' }}</h2>
            <div class="rounded-3xl overflow-hidden shadow-2xl border-8 border-white">
                <img-comparison-slider>
                    <img slot="first" src="{{ $getImg('home_transform_before', 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=2000') }}" />
                    <img slot="second" src="{{ $getImg('home_transform_after', 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?q=80&w=2000') }}" />
                </img-comparison-slider>
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