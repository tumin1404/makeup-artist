@extends('errors.layout')

@section('code', '503')
@section('title_text', 'Bảo Trì Hệ Thống')
@section('tagline', 'Nâng Cấp Trải Nghiệm')

@section('icon')
    <i class="fa-solid fa-wand-magic-sparkles"></i>
@endsection

@section('headline', 'Không Gian Đang Được Nâng Cấp')

@section('message')
    Chúng tôi đang tiến hành bảo trì và nâng cấp hệ thống để mang đến trải nghiệm làm đẹp và dịch vụ hoàn hảo nhất cho quý khách. Vui lòng quay lại trong giây lát!
@endsection

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5 cursor-pointer">
        <i class="fas fa-rotate-right"></i> Kiểm Tra Lại
    </button>
    @if(!empty($settings['hotline']))
        <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
            <i class="fas fa-phone-alt text-xs"></i> Hotline Tư Vấn
        </a>
    @else
        <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
            <i class="fas fa-house text-xs"></i> Về Trang Chủ
        </a>
    @endif
@endsection
