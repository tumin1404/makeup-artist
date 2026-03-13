<footer id="contact" class="bg-[#3e2f2f] text-white pt-24 pb-12 text-center">
    <div class="max-w-3xl mx-auto px-6" data-aos="fade-up">
        <h2 class="text-4xl md:text-5xl font-serif mb-6 text-[#c8a98d]">Sẵn sàng tỏa sáng?</h2>
        <p class="font-light text-white/70 mb-10 text-sm md:text-base">Liên hệ ngay để nhận tư vấn phong cách và đặt lịch makeup.</p>
        
        <div class="flex flex-col md:flex-row justify-center gap-6 mb-16">
            <a href="tel:{{ str_replace('.', '', $settings['hotline'] ?? '0866072800') }}" class="border border-[#c8a98d] text-[#c8a98d] hover:bg-[#c8a98d] hover:text-white px-8 py-3 rounded-full transition-all duration-300 uppercase text-xs tracking-widest flex items-center justify-center gap-2">
                <i class="fas fa-phone-alt text-sm"></i> {{ $settings['hotline'] ?? '0866.072.800' }}
            </a>
            @if(!empty($settings['social_zalo']))
                <a href="{{ $settings['social_zalo'] }}" target="_blank" class="bg-[#c8a98d] text-[#3e2f2f] hover:bg-white px-8 py-3 rounded-full transition-all duration-300 uppercase text-xs tracking-widest font-bold">
                    Nhắn tin Zalo
                </a>
            @endif
        </div>

        <div class="flex justify-center gap-8 mb-12 text-white/50 text-sm uppercase tracking-widest font-medium">
            @if(!empty($settings['social_facebook']))
                <a href="{{ $settings['social_facebook'] }}" target="_blank" class="hover:text-[#c8a98d] transition-colors">Facebook</a>
            @endif
            @if(!empty($settings['social_instagram']))
                <a href="{{ $settings['social_instagram'] }}" target="_blank" class="hover:text-[#c8a98d] transition-colors">Instagram</a>
            @endif
            @if(!empty($settings['social_tiktok']))
                <a href="{{ $settings['social_tiktok'] }}" target="_blank" class="hover:text-[#c8a98d] transition-colors">TikTok</a>
            @endif
        </div>

        <div class="border-t border-white/10 pt-8 text-[11px] text-white/40 font-light tracking-widest leading-loose uppercase">
            {!! nl2br(e($settings['footer_copyright'] ?? "© 2026 Luyện Thị Thảo Makeup Artist. All Rights Reserved. \n Hà Nội & Hưng Yên.")) !!}
        </div>
    </div>
</footer>