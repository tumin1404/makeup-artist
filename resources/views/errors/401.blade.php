@extends('errors.layout')

@section('code', '401')
@section('title_text', 'Yêu Cầu Xác Thực')
@section('tagline', 'Xác Thực Danh Tính')

@section('icon')
    <i class="fa-solid fa-user-lock"></i>
@endsection

@section('headline', 'Vui Lòng Đăng Nhập Để Tiếp Tục')

@section('message')
    Nội dung này yêu cầu đăng nhập tài khoản hợp lệ để xác định danh tính và cấp quyền trước khi xem.
@endsection

@section('actions')
    <a href="/admin/login" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5">
        <i class="fas fa-right-to-bracket"></i> Đăng Nhập Ngay
    </a>
    <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
        <i class="fas fa-house"></i> Về Trang Chủ
    </a>
@endsection
