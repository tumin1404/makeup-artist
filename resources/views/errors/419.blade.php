@extends('errors.layout')

@section('code', '419')
@section('title_text', 'Phiên Làm Việc Hết Hạn')
@section('tagline', 'Bảo Mật Biểu Mẫu (CSRF)')

@section('icon')
    <i class="fa-solid fa-clock-rotate-left"></i>
@endsection

@section('headline', 'Phiên Làm Việc Đã Hết Hạn')

@section('message')
    Biểu mẫu hoặc trang bạn đang thao tác đã ở trạng thái chờ quá lâu khiến mã xác thực bảo mật (CSRF token) hết hiệu lực. Vui lòng tải lại trang và thử lại.
@endsection

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5 cursor-pointer">
        <i class="fas fa-rotate-right"></i> Tải Lại Trang
    </button>
    <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
        <i class="fas fa-house"></i> Về Trang Chủ
    </a>
@endsection
