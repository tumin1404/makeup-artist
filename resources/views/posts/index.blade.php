@extends('layouts.app')

@section('title', 'Tạp Chí Làm Đẹp | Luyện Thị Thảo Makeup Artist')

@section('styles')
    <style>
        /* Hiệu ứng hover cho thẻ bài viết */
        .article-card img { transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
        .article-card:hover img { transform: scale(1.05); }
        
        .read-more-link {
            position: relative;
            display: inline-block;
        }
        .read-more-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: #c8a98d;
            transition: width 0.3s ease;
        }
        .read-more-link:hover::after { width: 100%; }
        .article-card:hover .arrow-icon { transform: translateX(5px); }
    </style>
@endsection

@section('content')
    <section class="relative h-[55vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 w-full h-full">
            <img src="https://images.unsplash.com/photo-1516975080664-ed2fc6a32937" alt="Beauty Journal" class="w-full h-full object-cover transform scale-105 animate-[pulse_20s_ease-in-out_infinite_alternate]">
            <div class="absolute inset-0 bg-dark/50"></div>
        </div>
        <div class="relative z-10 text-center text-white px-6 mt-16" data-aos="fade-up">
            <span class="block text-sm font-light tracking-[0.2em] uppercase mb-4 text-gold">The Beauty Journal</span>
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-4">Góc Chia Sẻ <span class="italic font-light">Kiến Thức</span></h1>
            <p class="text-lg font-light opacity-90 max-w-xl mx-auto">Nơi cập nhật những xu hướng trang điểm mới nhất và bí quyết chăm sóc sắc đẹp từ chuyên gia.</p>
        </div>
    </section>

    <section class="pt-16 pb-8 px-6 max-w-7xl mx-auto border-b border-dark/10" data-aos="fade-up">
        <div class="flex flex-wrap justify-center gap-6 md:gap-12 text-sm tracking-widest uppercase font-medium">
            <a href="{{ route('posts.index') }}" class="{{ !request('category') ? 'text-dark border-b-2 border-dark' : 'text-gray-400 hover:text-gold' }} pb-1 transition-colors">Tất cả bài viết</a>
            @foreach($categories as $category)
                <a href="{{ route('posts.index', ['category' => $category->slug]) }}" 
                   class="{{ request('category') == $category->slug ? 'text-dark border-b-2 border-dark' : 'text-gray-400 hover:text-gold' }} transition-colors">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </section>

    @if($featuredPost)
    <section class="py-20 px-6 max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row gap-12 items-center article-card cursor-pointer group" data-aos="fade-up">
            <div class="md:w-3/5 overflow-hidden rounded-2xl shadow-md">
                <a href="{{ route('posts.show', $featuredPost->slug) }}">
                    <img src="{{ asset('storage/' . $featuredPost->thumbnail) }}" alt="{{ $featuredPost->title }}" class="w-full aspect-video md:aspect-[4/3] object-cover">
                </a>
            </div>
            <div class="md:w-2/5 md:pr-8">
                <span class="text-gold text-xs tracking-widest uppercase mb-4 block font-bold">
                    {{ $featuredPost->category->name ?? 'Xu hướng' }} • Mới nhất
                </span>
                <h2 class="text-4xl md:text-5xl font-serif text-dark mb-6 leading-tight group-hover:text-gold transition-colors">
                    <a href="{{ route('posts.show', $featuredPost->slug) }}">{{ $featuredPost->title }}</a>
                </h2>
                <p class="font-light text-gray-600 mb-8 leading-relaxed">{{ Str::limit($featuredPost->excerpt, 180) }}</p>
                <a href="{{ route('posts.show', $featuredPost->slug) }}" class="read-more-link text-dark font-medium uppercase text-xs tracking-widest flex items-center w-max">
                    Đọc toàn bộ bài viết 
                    <i class="fas fa-long-arrow-alt-right ml-2 arrow-icon transition-transform"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    <section class="pb-32 px-6 max-w-7xl mx-auto">
        <div class="grid md:grid-cols-3 gap-12">
            @forelse($posts as $index => $post)
            <div class="article-card cursor-pointer group" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 150 }}">
                <div class="overflow-hidden rounded-xl mb-6 shadow-sm">
                    <a href="{{ route('posts.show', $post->slug) }}">
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full aspect-[4/3] object-cover">
                    </a>
                </div>
                <span class="text-gold text-[10px] tracking-widest uppercase mb-3 block font-bold">{{ $post->category->name ?? 'Tạp chí' }}</span>
                <h3 class="text-2xl font-serif text-dark mb-4 leading-snug group-hover:text-gold transition-colors">
                    <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                <p class="font-light text-gray-600 text-sm mb-6 line-clamp-3">{{ $post->excerpt }}</p>
                <a href="{{ route('posts.show', $post->slug) }}" class="read-more-link text-dark font-medium uppercase text-[10px] tracking-widest flex items-center w-max">
                    Đọc tiếp <i class="fas fa-long-arrow-alt-right ml-2 arrow-icon transition-transform"></i>
                </a>
            </div>
            @empty
                <p class="col-span-3 text-center text-gray-400 italic">Hiện tại chưa có bài viết nào trong mục này.</p>
            @endforelse
        </div>

        <div class="mt-20 flex justify-center items-center" data-aos="fade-up">
            {{ $posts->links('partials.pagination') }}
        </div>
    </section>
@endsection