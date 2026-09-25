@extends('layouts.app')

@section('title')
    @yield('code') - @yield('title_text', 'Thông Báo') | {{ $settings['site_name'] ?? 'Makeup Artist' }}
@endsection

@section('content')
<section class="relative min-h-[85vh] pt-32 pb-20 px-6 flex items-center justify-center overflow-hidden bg-gradient-to-b from-[#f6f1ec] via-[#faf7f4] to-[#f6f1ec]">
    {{-- Decorative Luxury Ambient Background Orbs --}}
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-72 h-72 bg-[#3e2f2f]/5 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute top-20 right-10 w-80 h-80 bg-gold/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-2xl w-full mx-auto text-center" data-aos="fade-up" data-aos-duration="800">
        
        {{-- Luxury Decorative Box --}}
        <div class="bg-white/80 backdrop-blur-md border border-white/80 shadow-2xl rounded-3xl p-8 md:p-14 relative overflow-hidden">
            
            {{-- Top Accent Line in Champagne Gold --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-gradient-to-r from-transparent via-gold to-transparent rounded-full"></div>

            {{-- Big Serif Number with Floating Badge --}}
            <div class="relative mb-6 flex items-center justify-center">
                <span class="font-serif text-8xl md:text-9xl font-bold tracking-wider text-gold/20 select-none">
                    @yield('code')
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-gold/20 to-white shadow-lg border border-gold/40 flex items-center justify-center text-gold text-3xl md:text-4xl transform hover:scale-105 transition-transform duration-300">
                        @yield('icon')
                    </div>
                </div>
            </div>

            {{-- Sub-label / Tagline --}}
            <span class="inline-block text-xs uppercase tracking-[0.35em] text-gold font-semibold mb-3">
                @yield('tagline', 'Makeup Artist & Beauty Studio')
            </span>

            {{-- Headline --}}
            <h1 class="text-2xl md:text-4xl font-serif font-bold text-dark mb-4 leading-tight">
                @yield('headline')
            </h1>

            {{-- Description / Explanation --}}
            <p class="text-gray-600 font-light text-base md:text-lg leading-relaxed mb-8 max-w-lg mx-auto">
                @yield('message')
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
                @yield('actions')
            </div>

            {{-- Admin Context Hint --}}
            @if(request()->is('admin*') || request()->is('filament*'))
                <div class="mt-4 pt-6 border-t border-gray-100 flex items-center justify-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-shield-halved text-gold"></i>
                    <span>Bạn đang ở khu vực quản trị:</span>
                    <a href="/admin" class="text-gold font-medium hover:underline">Quay lại Bảng Quản Trị</a>
                </div>
            @endif

            {{-- Quick Links for Visitor Assistance --}}
            <div class="mt-8 pt-8 border-t border-gray-100">
                <p class="text-xs uppercase tracking-widest text-gray-400 font-medium mb-4">Hoặc tiếp tục khám phá</p>
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-dark font-medium">
                    <a href="/" class="hover:text-gold transition-colors flex items-center gap-1.5">
                        <i class="fas fa-house text-xs text-gold"></i> Trang chủ
                    </a>
                    <a href="/services" class="hover:text-gold transition-colors flex items-center gap-1.5">
                        <i class="fas fa-wand-magic-sparkles text-xs text-gold"></i> Dịch vụ
                    </a>
                    <a href="/portfolio" class="hover:text-gold transition-colors flex items-center gap-1.5">
                        <i class="fas fa-camera text-xs text-gold"></i> Bộ sưu tập
                    </a>
                    <a href="/booking" class="hover:text-gold transition-colors flex items-center gap-1.5">
                        <i class="fas fa-calendar-check text-xs text-gold"></i> Đặt lịch hẹn
                    </a>
                </div>
            </div>

        </div>

        {{-- Need Quick Help Section --}}
        @if(!empty($settings['hotline']))
            <div class="mt-8 text-center text-xs text-gray-500 font-light">
                Cần hỗ trợ gấp? Gọi ngay hotline: 
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}" class="text-gold font-medium hover:underline">
                    {{ $settings['hotline'] }}
                </a>
            </div>
        @endif

    </div>
</section>
@endsection
