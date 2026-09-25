@extends('errors.layout')

@section('code', '404')
@section('title_text', 'Không Tìm Thấy Trang')
@section('tagline', 'Trang Không Khả Dụng')

@section('icon')
    <i class="fa-regular fa-compass"></i>
@endsection

@section('headline', 'Không Tìm Thấy Trang Yêu Cầu')

@section('message')
    Trang bạn đang tìm kiếm có thể đã đổi địa chỉ, bị gỡ bỏ hoặc tạm thời không khả dụng trong bộ sưu tập của chúng tôi.
@endsection

@section('actions')
    <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5">
        <i class="fas fa-arrow-left"></i> Về Trang Chủ
    </a>
    <a href="/services" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
        <i class="fas fa-wand-magic-sparkles"></i> Xem Dịch Vụ
    </a>
@endsection
