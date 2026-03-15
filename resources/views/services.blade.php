@extends('layouts.app')

@section('title', 'Dịch Vụ & Báo Giá | Luyện Thị Thảo Makeup Artist')

@php
    $getImg = fn($key, $default) => empty($settings[$key]) ? $default : (str_starts_with($settings[$key], 'http') ? $settings[$key] : asset('storage/' . $settings[$key]));
@endphp

@section('styles')
    <style>
        .service-row {
            border-bottom: 1px solid rgba(200, 169, 141, 0.3);
            transition: all 0.4s ease;
        }
        .service-row:hover {
            background-color: #ffffff;
            transform: translateX(10px);
            border-bottom-color: transparent;
            box-shadow: -10px 10px 30px rgba(0,0,0,0.02);
        }
        .process-line::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, #c8a98d 0%, transparent 100%);
            z-index: 0;
        }
        @media (max-width: 768px) {
            .process-line::before { display: none; }
        }
    </style>
@endsection

@section('content')
    <section class="relative h-[50vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 w-full h-full">
            <img src="{{ $getImg('service_hero_bg', 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2') }}" class="w-full h-full object-cover transform scale-105 animate-[pulse_20s_ease-in-out_infinite_alternate]">
            <div class="absolute inset-0 bg-dark/60"></div>
        </div>
        <div class="relative z-10 text-center text-white px-6 mt-16" data-aos="fade-up">
            <span class="block text-sm font-light tracking-[0.2em] uppercase mb-4 text-gold">Premium Services</span>
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-4">
                {{ $settings['service_hero_title'] ?? 'Gói Dịch Vụ' }} <span class="italic font-light text-primary">{{ $settings['service_hero_subtitle'] ?? 'Đặc Quyền' }}</span>
            </h1>
        </div>
    </section>

    <section class="py-12 bg-white border-b border-primary" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-6 font-medium">Sử dụng 100% mỹ phẩm cao cấp chuẩn quốc tế</p>
            <div class="flex flex-wrap justify-center items-center gap-10 md:gap-20 opacity-60 grayscale">
                <span class="font-serif text-xl tracking-widest">DIOR</span>
                <span class="font-sans font-bold text-xl tracking-widest">MAC</span>
                <span class="font-serif text-xl tracking-widest">TOM FORD</span>
                <span class="font-sans text-xl tracking-widest uppercase">Nars</span>
                <span class="font-serif text-xl tracking-widest text-center leading-tight">CHARLOTTE<br>TILBURY</span>
            </div>
        </div>
    </section>

    <section class="py-32 px-6 max-w-5xl mx-auto">
        <div class="text-center mb-24" data-aos="fade-up">
            <span class="text-gold text-sm tracking-widest uppercase mb-2 block">Menu Fine-Dining</span>
            <h2 class="text-4xl md:text-5xl font-serif text-dark">Bảng Giá Dịch Vụ</h2>
            <p class="mt-4 font-light text-gray-600">Đầu tư vào nhan sắc là khoản đầu tư vô giá. Mọi gói dịch vụ đều được cá nhân hóa.</p>
        </div>

        <div class="space-y-6">
            @forelse($services as $index => $service)
                <div class="service-row p-8 rounded-2xl flex flex-col md:flex-row gap-8 items-start md:items-center" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="md:w-1/3">
                        <h3 class="text-2xl font-serif text-dark mb-2">{{ $service->name }}</h3>
                        <p class="text-gold font-medium tracking-widest uppercase text-sm">{{ $service->price_text }}</p>
                    </div>
                    <div class="md:w-2/3">
                        <p class="font-light text-gray-700 leading-relaxed mb-4">{{ $service->description }}</p>
                        
                        @php $features = json_decode($service->features, true); @endphp
                        @if(!empty($features) && is_array($features))
                            <ul class="text-sm font-light text-gray-500 space-y-2">
                                @foreach($features as $feature)
                                    <li><i class="fas fa-check text-gold mr-2 text-xs"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    Chưa có dịch vụ nào được cập nhật.
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <p class="text-xs italic text-gray-400">* Báo giá trên có thể thay đổi tùy thuộc vào vị trí di chuyển và yêu cầu concept cụ thể.</p>
        </div>
    </section>

    <section class="py-32 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-24" data-aos="fade-up">
                <span class="text-gold text-sm tracking-widest uppercase mb-2 block">How it works</span>
                <h2 class="text-4xl md:text-5xl font-serif text-dark">{{ $settings['process_title'] ?? 'Quy Trình Làm Việc' }}</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-10 relative">
                <div class="text-center relative process-line z-10" data-aos="fade-right" data-aos-delay="0">
                    <div class="w-12 h-12 mx-auto bg-primary text-gold border border-gold rounded-full flex items-center justify-center font-serif text-xl mb-6 relative z-10">1</div>
                    <h4 class="text-lg font-serif text-dark mb-3">{{ $settings['process_step_1_title'] ?? 'Tư Vấn & Đặt Lịch' }}</h4>
                    <p class="font-light text-sm text-gray-600 px-4">{{ $settings['process_step_1_desc'] ?? 'Lắng nghe mong muốn, phân tích tình trạng da và chốt concept phù hợp nhất.' }}</p>
                </div>
                
                <div class="text-center relative process-line z-10" data-aos="fade-right" data-aos-delay="150">
                    <div class="w-12 h-12 mx-auto bg-primary text-gold border border-gold rounded-full flex items-center justify-center font-serif text-xl mb-6 relative z-10">2</div>
                    <h4 class="text-lg font-serif text-dark mb-3">{{ $settings['process_step_2_title'] ?? 'Giai Đoạn Chuẩn Bị' }}</h4>
                    <p class="font-light text-sm text-gray-600 px-4">{{ $settings['process_step_2_desc'] ?? 'Hướng dẫn bạn cách skincare trước ngày quan trọng để lớp nền ăn tệp hoàn hảo.' }}</p>
                </div>
                
                <div class="text-center relative process-line z-10" data-aos="fade-right" data-aos-delay="300">
                    <div class="w-12 h-12 mx-auto bg-primary text-gold border border-gold rounded-full flex items-center justify-center font-serif text-xl mb-6 relative z-10">3</div>
                    <h4 class="text-lg font-serif text-dark mb-3">{{ $settings['process_step_3_title'] ?? 'Ngày Tỏa Sáng' }}</h4>
                    <p class="font-light text-sm text-gray-600 px-4">{{ $settings['process_step_3_desc'] ?? 'Đến đúng giờ, chuẩn bị 100% dụng cụ vô trùng và thực hiện makeup chuyên nghiệp.' }}</p>
                </div>
                
                <div class="text-center relative z-10" data-aos="fade-right" data-aos-delay="450">
                    <div class="w-12 h-12 mx-auto bg-gold text-white rounded-full flex items-center justify-center font-serif text-xl mb-6 shadow-lg">4</div>
                    <h4 class="text-lg font-serif text-gold mb-3">{{ $settings['process_step_4_title'] ?? 'Hoàn Thiện' }}</h4>
                    <p class="font-light text-sm text-gray-600 px-4">{{ $settings['process_step_4_desc'] ?? 'Dặm lại lần cuối, mặc trang phục và lưu lại những khung hình rạng rỡ nhất.' }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection