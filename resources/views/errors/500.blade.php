@extends('errors.layout')

@section('code', '500')
@section('title_text', 'Sự Cố Máy Chủ')
@section('tagline', 'Hệ Thống Đang Xử Lý')

@section('icon')
    <i class="fa-solid fa-triangle-exclamation"></i>
@endsection

@section('headline', 'Đã Xảy Ra Sự Cố Kỹ Thuật')

@section('message')
    Hệ thống máy chủ gặp gián đoạn bất ngờ trong lúc xử lý yêu cầu của bạn. Chúng tôi đã ghi nhận thông tin sự cố và đang khẩn trương khắc phục.
@endsection

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5 cursor-pointer">
        <i class="fas fa-rotate-right"></i> Tải Lại Trang
    </button>
    <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
        <i class="fas fa-house"></i> Về Trang Chủ
    </a>
@endsection
