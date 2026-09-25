@extends('errors.layout')

@section('code', '403')
@section('title_text', 'Truy Cập Bị Hạn Chế')
@section('tagline', 'Bảo Mật & Phân Quyền')

@section('icon')
    <i class="fa-solid fa-lock"></i>
@endsection

@section('headline', 'Khu Vực Bị Hạn Chế Truy Cập')

@section('message')
    Bạn không có đủ đặc quyền để xem hoặc thao tác trên tài nguyên này. Nếu bạn là quản trị viên hoặc nhân viên, vui lòng đăng nhập bằng tài khoản có quyền truy cập tương ứng.
@endsection

@section('actions')
    <a href="/admin/login" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gold text-white px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:bg-[#b5977d] transition-all duration-300 shadow-lg shadow-gold/25 hover:shadow-xl transform hover:-translate-y-0.5">
        <i class="fas fa-right-to-bracket"></i> Đăng Nhập Quản Trị
    </a>
    <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-dark/20 text-dark px-8 py-3.5 rounded-full text-xs font-semibold uppercase tracking-widest hover:border-gold hover:text-gold transition-all duration-300">
        <i class="fas fa-house"></i> Về Trang Chủ
    </a>
@endsection
