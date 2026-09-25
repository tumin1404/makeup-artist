@extends('layouts.app')

@section('title', $post->title . ' | ' . ($settings['site_name'] ?? ''))
@section('meta_description', Str::limit(strip_tags($post->summary ?? $post->content), 160))

@section('meta')
    @php
        $postImg = !empty($post->thumbnail) ? asset('storage/' . $post->thumbnail) : (!empty($settings['site_meta_image']) ? asset('storage/' . $settings['site_meta_image']) : asset('images/default-share.jpg'));
    @endphp
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($post->summary ?? $post->content), 160) }}">
    <meta property="og:image" content="{{ $postImg }}">
    <meta property="article:published_time" content="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $post->author?->name ?? ($settings['author_name'] ?? ($settings['site_name'] ?? 'Makeup Artist')) }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($post->summary ?? $post->content), 160) }}">
    <meta name="twitter:image" content="{{ $postImg }}">

    @php
        $schemaArticle = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => Str::limit(strip_tags($post->summary ?? $post->content), 160),
            'image' => $postImg,
            'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author?->name ?? ($settings['author_name'] ?? 'Makeup Artist'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $settings['site_name'] ?? 'Makeup Artist',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => !empty($settings['site_logo']) ? asset('storage/' . $settings['site_logo']) : asset('favicon.ico'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];
    @endphp
    {{-- JSON-LD Article Schema --}}
    <script type="application/ld+json">
    {!! json_encode($schemaArticle, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@php
    $getImg = fn($key, $default) => empty($settings[$key]) ? $default : (str_starts_with($settings[$key], 'http') ? $settings[$key] : asset('storage/' . $settings[$key]));
@endphp

@section('styles')
    <style>
        /* Tối ưu hóa cho nội dung từ CMS */
        .article-content p {
            font-family: var(--font-sans);
            font-weight: 300;
            line-height: 1.8;
            color: #4a3f3f;
            margin-bottom: 1.5rem;
            font-size: 1.125rem;
        }
        
        /* Drop cap - Chữ cái lớn đầu đoạn */
        .article-content > p:first-of-type::first-letter {
            font-family: var(--font-serif);
            font-size: 4rem;
            float: left;
            line-height: 0.8;
            margin-right: 0.75rem;
            color: var(--color-gold);
            font-weight: bold;
        }

        .article-content h2 {
            font-family: var(--font-serif);
            font-size: 2rem;
            color: var(--color-dark);
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .article-content img {
            width: 100%;
            border-radius: 0.75rem;
            margin: 2.5rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .article-content blockquote {
            border-left: 2px solid var(--color-gold);
            padding-left: 1.5rem;
            margin: 2.5rem 0;
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-style: italic;
            color: var(--color-gold);
            line-height: 1.6;
        }

        .article-content ul {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 1.5rem;
        }

        .article-content ul li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
            font-weight: 300;
        }

        .article-content ul li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--color-gold);
            font-size: 1.2rem;
        }
        .article-content figcaption {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <article class="pt-32 pb-20">
        <header class="max-w-4xl mx-auto px-6 text-center mb-16" data-aos="fade-up">
            <a href="{{ route('posts.index', ['category' => $post->category->slug ?? '']) }}" class="text-gold text-xs tracking-widest uppercase font-bold hover:text-dark transition-colors mb-6 inline-block">
                {{ $post->category->name ?? 'Xu Hướng' }}
            </a>
            <h1 class="text-4xl md:text-6xl font-serif text-dark mb-8 leading-tight">{{ $post->title }}</h1>
            
            <div class="flex items-center justify-center gap-4 text-sm font-light text-gray-500 uppercase tracking-wide">
                <span><i class="far fa-calendar-alt mr-2"></i> {{ $post->published_at ? $post->published_at->format('d/m/Y') : $post->created_at->format('d/m/Y') }}</span>
                <span class="w-1 h-1 bg-gold rounded-full mx-2"></span>
                <span><i class="far fa-clock mr-2"></i> 5 phút đọc</span>
            </div>
        </header>

        <div class="max-w-6xl mx-auto px-6 mb-20" data-aos="fade-up" data-aos-delay="100">
            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-full aspect-video md:aspect-[21/9] object-cover rounded-2xl shadow-xl">
        </div>

        <div class="max-w-3xl mx-auto px-6 article-content" data-aos="fade-up">
            {!! $post->content !!}
        </div>

        <footer class="max-w-3xl mx-auto px-6 mt-16 pt-10 border-t border-dark/10" data-aos="fade-up">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex gap-3">
                    <span class="text-xs font-medium uppercase tracking-widest text-dark bg-white border border-dark/10 px-4 py-2 rounded-full">#Bridal2026</span>
                    <span class="text-xs font-medium uppercase tracking-widest text-dark bg-white border border-dark/10 px-4 py-2 rounded-full">#NudeLuxury</span>
                </div>
                
                <div class="flex items-center gap-4 text-sm font-medium uppercase tracking-widest text-dark">
                    <span>Chia sẻ:</span>
                    <a href="{{ $settings['social_facebook'] ?? '#' }}" target="_blank" class="hover:text-gold transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="{{ $settings['social_instagram'] ?? '#' }}" target="_blank" class="hover:text-gold transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="javascript:void(0)" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã copy link!');" class="hover:text-gold transition-colors"><i class="fas fa-link"></i></a>
                </div>
            </div>

            <div class="mt-16 bg-white p-8 rounded-2xl flex items-center gap-6 shadow-sm">
                <img src="{{ $getImg('author_avatar', 'https://images.unsplash.com/photo-1596462502278-27bfdc403348') }}" alt="Author" class="w-20 h-20 rounded-full object-cover opacity-90 shadow-md">
                <div>
                    <h4 class="font-serif text-xl text-dark mb-1">{{ $settings['author_name'] ?? '' }}</h4>
                    <p class="font-light text-sm text-gray-500 uppercase tracking-widest mb-2">{{ $settings['author_title'] ?? 'Makeup Artist / Founder' }}</p>
                    <p class="font-light text-sm text-gray-600 leading-relaxed">{{ $settings['author_bio'] ?? 'Đam mê tôn vinh vẻ đẹp nguyên bản thông qua phong cách trang điểm Nude Luxury chuyên nghiệp.' }}</p>
                </div>
            </div>
        </footer>
    </article>

    @if(isset($relatedPosts) && $relatedPosts->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-serif text-dark text-center mb-16 italic">Có Thể Bạn Sẽ Thích</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                @foreach($relatedPosts as $rPost)
                <div class="group cursor-pointer flex gap-6 items-center" data-aos="fade-up">
                    <div class="w-1/3 overflow-hidden rounded-xl">
                        <a href="{{ route('posts.show', $rPost->slug) }}">
                            <img src="{{ asset('storage/' . $rPost->thumbnail) }}" class="w-full aspect-square object-cover transform group-hover:scale-105 transition-transform duration-700">
                        </a>
                    </div>
                    <div class="w-2/3">
                        <span class="text-gold text-[10px] tracking-widest uppercase mb-2 block font-bold">{{ $rPost->category->name ?? 'Góc làm đẹp' }}</span>
                        <h3 class="text-xl font-serif text-dark mb-3 group-hover:text-gold transition-colors">
                            <a href="{{ route('posts.show', $rPost->slug) }}">{{ $rPost->title }}</a>
                        </h3>
                        <span class="text-xs font-light text-gray-400">{{ $rPost->published_at ? $rPost->published_at->format('d/m/Y') : $rPost->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection