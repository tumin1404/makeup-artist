<footer id="contact" class="bg-dark text-white pt-24 pb-12 text-center" style="background-color: var(--color-dark);">
    <div class="max-w-3xl mx-auto px-6" data-aos="fade-up">
        <h2 class="text-4xl md:text-5xl font-serif mb-6 text-gold">{{ $settings['footer_cta_title'] ?? 'Sẵn sàng tỏa sáng?' }}</h2>
        <p class="font-light text-white/70 mb-10 text-sm md:text-base">{{ $settings['footer_cta_desc'] ?? 'Liên hệ ngay để nhận tư vấn phong cách và đặt lịch makeup.' }}</p>
        
        <div class="flex flex-col md:flex-row justify-center items-center gap-6 mb-16">
            @if(!empty($settings['hotline']))
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}" class="border border-gold text-gold hover:bg-gold hover:text-white px-8 py-3 rounded-full transition-all duration-300 uppercase text-xs tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-phone-alt text-sm"></i> {{ $settings['hotline'] }}
                </a>
            @endif
            @if(!empty($settings['social_zalo']))
                <a href="{{ $settings['social_zalo'] }}" target="_blank" class="bg-gold text-dark hover:bg-white px-8 py-3 rounded-full transition-all duration-300 uppercase text-xs tracking-widest font-bold">
                    Nhắn tin Zalo
                </a>
            @endif
            @if(!empty($settings['email']))
                <a href="mailto:{{ $settings['email'] }}" class="border border-white/20 text-white/80 hover:text-white hover:border-gold px-8 py-3 rounded-full transition-all duration-300 uppercase text-xs tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-envelope text-sm"></i> {{ $settings['email'] }}
                </a>
            @endif
        </div>

        <div class="flex flex-wrap justify-center gap-8 mb-12 text-white/50 text-sm uppercase tracking-widest font-medium">
            @if(!empty($settings['social_facebook']))
                <a href="{{ $settings['social_facebook'] }}" target="_blank" class="hover:text-gold transition-colors">Facebook</a>
            @endif
            @if(!empty($settings['social_instagram']))
                <a href="{{ $settings['social_instagram'] }}" target="_blank" class="hover:text-gold transition-colors">Instagram</a>
            @endif
            @if(!empty($settings['social_tiktok']))
                <a href="{{ $settings['social_tiktok'] }}" target="_blank" class="hover:text-gold transition-colors">TikTok</a>
            @endif
            @if(!empty($settings['social_youtube']))
                <a href="{{ $settings['social_youtube'] }}" target="_blank" class="hover:text-gold transition-colors">YouTube</a>
            @endif
        </div>

        @if(!empty($settings['address_main']) || !empty($settings['address']))
            <div class="text-xs text-white/60 mb-6 font-light">
                <i class="fas fa-map-marker-alt text-gold mr-1"></i> {{ $settings['address_main'] ?? $settings['address'] }}
            </div>
        @endif

        <div class="border-t border-white/10 pt-8 text-[11px] text-white/40 font-light tracking-widest leading-loose uppercase">
            {!! nl2br(e($settings['footer_copyright'] ?? "© " . date('Y') . " " . ($settings['site_name'] ?? 'Makeup Artist') . ". All Rights Reserved.")) !!}
        </div>
    </div>
</footer>