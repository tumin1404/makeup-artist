@extends('errors.layout')

@section('code', '429')
@section('title_text', 'Quá Nhiều Yêu Cầu')
@section('tagline', 'Giới Hạn Tần Suất (Rate Limit)')

@section('icon')
    <i class="fa-solid fa-bolt"></i>
@endsection

@section('headline', 'Yêu Cầu Quá Dồn Dập')

@section('message')
    Hệ thống phát hiện có quá nhiều lượt gửi yêu cầu trong thời gian ngắn từ thiết bị của bạn. Để đảm bảo an toàn và tính ổn định, vui lòng tạm nghỉ vài giây trước khi tiếp tục.
@endsection

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5 cursor-pointer">
        <i class="fas fa-rotate-right"></i> Thử Lại
    </button>
    <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
        <i class="fas fa-house"></i> Về Trang Chủ
    </a>
@endsection
