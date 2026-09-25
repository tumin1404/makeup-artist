<x-filament-panels::page>
    <div class="space-y-8">
        {{-- THẺ SHOWCASE CÁC BỘ PRESETS MẪU 1-CLICK --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="text-xl">✨</span> Bộ Sưu Tập Giao Diện Chuẩn Ngành (1-Click Presets)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Click chọn nhanh mẫu giao diện để tự động nạp bảng màu & phông chữ vào form bên dưới, sau đó bấm <strong>Lưu & Cập Nhật Giao Diện</strong> để áp dụng lên website.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @foreach(\App\Filament\Pages\ThemeSettings::getPresets() as $pKey => $preset)
                    <button 
                        type="button" 
                        wire:click="applyPreset('{{ $pKey }}')" 
                        class="p-4 rounded-xl text-left border transition-all duration-300 hover:shadow-md hover:scale-[1.02] flex flex-col justify-between group cursor-pointer {{ ($data['theme_preset'] ?? '') === $pKey ? 'border-primary-500 bg-primary-50/30 dark:bg-primary-950/20 ring-2 ring-primary-500/20' : 'border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-700' }}"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-sm text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $preset['name'] }}
                                </span>
                                @if(($data['theme_preset'] ?? '') === $pKey)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-primary-500 text-white font-medium">Đang chọn</span>
                                @endif
                            </div>

                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed mb-3 line-clamp-2">
                                {{ $preset['description'] }}
                            </p>
                        </div>

                        <div>
                            {{-- Color Palette Dots --}}
                            <div class="flex items-center gap-1.5 mb-2">
                                <span class="w-5 h-5 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $preset['primary'] }};" title="Nền: {{ $preset['primary'] }}"></span>
                                <span class="w-5 h-5 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $preset['gold'] }};" title="Điểm nhấn: {{ $preset['gold'] }}"></span>
                                <span class="w-5 h-5 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $preset['dark'] }};" title="Tối: {{ $preset['dark'] }}"></span>
                                <span class="w-5 h-5 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $preset['button'] }};" title="Nút bấm: {{ $preset['button'] }}"></span>
                            </div>

                            <div class="text-[10px] text-gray-400 flex items-center gap-1 font-mono">
                                <span>Font: {{ $preset['heading_font'] }}</span>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- FORM CẤU HÌNH VÀ LIVE PREVIEW --}}
        <form wire:submit="submit" class="space-y-8">
            {{ $this->form }}

            {{-- LIVE PREVIEW STUDIO CARD --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <span>👁️</span> Live Preview Studio (Xem Trước Giao Diện Thực Tế)
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Bản mô phỏng tương tác trực quan các thành phần giao diện theo cấu hình đang chọn.
                        </p>
                    </div>
                </div>

                {{-- MOCKUP CONTAINER --}}
                @php
                    $prevPrimary = $data['theme_color_primary'] ?? '#f6f1ec';
                    $prevGold = $data['theme_color_gold'] ?? '#c8a98d';
                    $prevDark = $data['theme_color_dark'] ?? '#3e2f2f';
                    $prevBtn = $data['theme_color_button'] ?? '#3e2f2f';
                    $prevBtnText = $data['theme_color_button_text'] ?? '#ffffff';
                    $prevHeadFont = ($data['theme_font_heading_type'] ?? 'google') === 'custom' ? 'CustomHeadingFont, serif' : ($data['theme_font_heading'] ?? 'Playfair Display') . ', serif';
                    $prevBodyFont = ($data['theme_font_body_type'] ?? 'google') === 'custom' ? 'CustomBodyFont, sans-serif' : ($data['theme_font_body'] ?? 'Inter') . ', sans-serif';
                @endphp

                <div class="rounded-2xl p-6 sm:p-10 border transition-all duration-300" style="background-color: {{ $prevPrimary }}; border-color: {{ $prevGold }}40;">
                    <div class="max-w-3xl mx-auto space-y-8">
                        {{-- Mockup Top Header --}}
                        <div class="flex items-center justify-between pb-6 border-b" style="border-color: {{ $prevGold }}30;">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm" style="background-color: {{ $prevGold }}; color: {{ $prevDark }}; font-family: {{ $prevHeadFont }};">
                                    TM
                                </div>
                                <div>
                                    <h4 class="text-base font-bold leading-tight" style="color: {{ $prevDark }}; font-family: {{ $prevHeadFont }};">
                                        Thảo Makeup Artist
                                    </h4>
                                    <span class="text-[10px] tracking-widest uppercase block" style="color: {{ $prevGold }}; font-family: {{ $prevBodyFont }};">
                                        Haute Beauty & Bridal Studio
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 text-xs font-medium" style="color: {{ $prevDark }}; font-family: {{ $prevBodyFont }};">
                                <span class="hidden sm:inline-block opacity-80">Dịch Vụ</span>
                                <span class="hidden sm:inline-block opacity-80">Bộ Sưu Tập</span>
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider" style="background-color: {{ $prevBtn }}; color: {{ $prevBtnText }};">
                                    Đặt Lịch
                                </span>
                            </div>
                        </div>

                        {{-- Mockup Hero Content --}}
                        <div class="text-center sm:text-left space-y-4">
                            <span class="inline-block text-xs uppercase tracking-[0.25em] font-semibold px-3 py-1 rounded-full border" style="color: {{ $prevGold }}; border-color: {{ $prevGold }}60; background-color: {{ $prevGold }}15; font-family: {{ $prevBodyFont }};">
                                Signature Beauty Layout
                            </span>

                            <h1 class="text-3xl sm:text-5xl font-bold leading-tight" style="color: {{ $prevDark }}; font-family: {{ $prevHeadFont }};">
                                Nghệ Thuật Tôn Vinh <br>
                                <span class="font-light italic">Vẻ Đẹp Nguyên Bản</span>
                            </h1>

                            <p class="text-sm sm:text-base font-light leading-relaxed max-w-xl" style="color: {{ $prevDark }}cc; font-family: {{ $prevBodyFont }};">
                                Trải nghiệm dịch vụ makeup chuyên nghiệp chuẩn Nude Luxury cho cô dâu và sự kiện cao cấp. Mỗi layout là một tác phẩm được chăm chút tỉ mỉ.
                            </p>
                        </div>

                        {{-- Mockup Buttons & Cards --}}
                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <button type="button" class="px-8 py-3.5 rounded-full text-xs uppercase tracking-widest font-semibold shadow-lg transition-all" style="background-color: {{ $prevBtn }}; color: {{ $prevBtnText }}; font-family: {{ $prevBodyFont }};">
                                Gửi Yêu Cầu Đặt Lịch
                            </button>

                            <button type="button" class="px-6 py-3.5 rounded-full text-xs uppercase tracking-widest font-semibold border transition-all" style="border-color: {{ $prevGold }}; color: {{ $prevDark }}; background-color: {{ $prevPrimary }}; font-family: {{ $prevBodyFont }};">
                                Xem Bảng Giá
                            </button>
                        </div>

                        {{-- Mockup Feature Tags & Swatches --}}
                        <div class="pt-6 border-t grid grid-cols-2 sm:grid-cols-4 gap-3 text-center" style="border-color: {{ $prevGold }}30;">
                            <div class="p-3 rounded-xl bg-white/70 backdrop-blur-sm border" style="border-color: {{ $prevGold }}30;">
                                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Màu Nền</span>
                                <span class="font-mono text-xs font-bold" style="color: {{ $prevDark }};">{{ $prevPrimary }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/70 backdrop-blur-sm border" style="border-color: {{ $prevGold }}30;">
                                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Màu Điểm Nhấn</span>
                                <span class="font-mono text-xs font-bold" style="color: {{ $prevGold }};">{{ $prevGold }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/70 backdrop-blur-sm border" style="border-color: {{ $prevGold }}30;">
                                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Font Tiêu Đề</span>
                                <span class="text-xs font-bold truncate block" style="color: {{ $prevDark }}; font-family: {{ $prevHeadFont }};">{{ ($data['theme_font_heading_type'] ?? '') === 'custom' ? 'Font Tải Lên' : ($data['theme_font_heading'] ?? 'Playfair Display') }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/70 backdrop-blur-sm border" style="border-color: {{ $prevGold }}30;">
                                <span class="text-[10px] uppercase font-semibold text-gray-500 block mb-1">Font Nội Dung</span>
                                <span class="text-xs font-bold truncate block" style="color: {{ $prevDark }}; font-family: {{ $prevBodyFont }};">{{ ($data['theme_font_body_type'] ?? '') === 'custom' ? 'Font Tải Lên' : ($data['theme_font_body'] ?? 'Inter') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NÚT LƯU CẤU HÌNH --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                <x-filament::button type="submit" size="xl" icon="heroicon-o-check" color="primary">
                    Lưu & Cập Nhật Giao Diện Website
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
