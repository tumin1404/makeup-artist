@extends('layouts.app')

@section('title', 'Liên Hệ & Đặt Lịch | ' . ($settings['site_name'] ?? ''))

@section('styles')
    <style>
        .form-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(62, 47, 47, 0.2);
            padding: 12px 0;
            width: 100%;
            transition: all 0.4s ease;
            outline: none;
            font-weight: 300;
        }
        .form-input:focus {
            border-bottom-color: #c8a98d;
            padding-left: 8px;
        }
        .form-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #c8a98d;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .map-container iframe {
            filter: grayscale(100%) invert(5%) sepia(10%) hue-rotate(320deg) saturate(300%) brightness(95%);
        }
        
        /* Custom Checkbox Styles */
        .service-checkbox {
            display: none;
        }
        .service-label {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 1px solid rgba(62, 47, 47, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 300;
            color: #3e2f2f;
        }
        .service-checkbox:checked + .service-label {
            border-color: #c8a98d;
            background-color: rgba(200, 169, 141, 0.05);
            font-weight: 400;
        }
        .service-checkbox:checked + .service-label .check-icon {
            opacity: 1;
            transform: scale(1);
        }
        .check-icon {
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
            color: #c8a98d;
        }
    </style>
@endsection

@section('content')
    <section class="pt-40 pb-20 px-6 max-w-7xl mx-auto">
        <div class="text-center md:text-left mb-20" data-aos="fade-up">
            <span class="text-gold text-sm tracking-widest uppercase mb-2 block" style="color: #c8a98d;">Connect with us</span>
            <h1 class="text-5xl md:text-7xl font-serif text-dark leading-tight" style="color: #3e2f2f;">Gửi Lời Nhắn <br> <span class="italic font-light">Cho Thảo</span></h1>
        </div>

        <div class="flex flex-col lg:flex-row gap-20">
            {{-- THÔNG TIN LIÊN HỆ (BÊN TRÁI) --}}
            <div class="lg:w-4/12 space-y-12" data-aos="fade-right" data-aos-delay="200">
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-bold mb-6" style="color: #c8a98d;">Thông tin liên lạc</h3>
                    <div class="space-y-4 font-light text-lg text-gray-800">
                        <p class="hover:text-[#c8a98d] transition-colors">
                            <a href="tel:{{ str_replace('.', '', $settings['hotline'] ?? '0866072800') }}">
                                {{ $settings['hotline'] ?? '0866.072.800' }}
                            </a>
                        </p>
                        <p class="hover:text-[#c8a98d] transition-colors">
                            <a href="mailto:{{ $settings['email'] ?? 'contact@luyenthithao.com' }}">
                                {{ $settings['email'] ?? 'contact@luyenthithao.com' }}
                            </a>
                        </p>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-bold mb-6" style="color: #c8a98d;">Địa điểm làm việc</h3>
                    <div class="space-y-4 font-light text-lg text-gray-800">
                        <p>Hà Nội: {{ $settings['address_hanoi'] ?? 'Studio Nude Luxury, Q. Cầu Giấy' }}</p>
                        <p>Hưng Yên: {{ $settings['address_hungyen'] ?? 'TP. Hưng Yên & Lân cận' }}</p>
                        <p class="text-sm italic text-gray-500 mt-2">* Nhận booking toàn quốc cho cô dâu & sự kiện.</p>
                    </div>
                </div>
            </div>

            {{-- FORM ĐẶT LỊCH (BÊN PHẢI) --}}
            <div class="lg:w-8/12 bg-white p-10 md:p-16 rounded-2xl shadow-sm relative overflow-hidden" data-aos="fade-left" data-aos-delay="400">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#f6f1ec] rounded-bl-full opacity-50"></div>
                
                <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-10 relative z-10">
                    @csrf
                    
                    {{-- THÔNG TIN CÁ NHÂN --}}
                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="group">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" name="customer_name" class="form-input" placeholder="VD: Nguyễn Văn A" required>
                        </div>
                        <div class="group">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" name="phone" class="form-input" placeholder="VD: 090 123 4567" required>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="group">
                            <label class="form-label">Số Zalo (Để tư vấn)</label>
                            <input type="tel" name="zalo" class="form-input" placeholder="VD: 090 123 4567">
                        </div>
                        <div class="group">
                            <label class="form-label">Link Facebook / Instagram</label>
                            <input type="text" name="social_link" class="form-input" placeholder="Để Thảo xem phong cách của bạn">
                        </div>
                    </div>

                    {{-- CHỌN NGÀY THÁNG --}}
                    <div class="group">
                        <label class="form-label">Ngày bắt đầu dự kiến *</label>
                        <input type="date" name="booking_date" class="form-input" required>
                        <p class="text-xs text-gray-400 mt-2 italic">* Nếu sự kiện diễn ra trong nhiều ngày (VD: Ăn hỏi + Cưới), vui lòng chọn ngày khởi đầu. Thảo sẽ chốt chi tiết lịch trình các ngày sau khi tư vấn.</p>
                    </div>

                    {{-- CHỌN NHIỀU DỊCH VỤ CÓ PHÂN LOẠI --}}
                    <div class="group pt-4">
                        <label class="form-label mb-6">Bạn quan tâm đến dịch vụ nào? (Có thể chọn nhiều)</label>
                        
                        {{-- Gom nhóm dịch vụ theo category hoặc service_level (Giả định ở Controller đã nhóm bằng ->groupBy('service_level')) --}}
                        @php
                            // Chuyển mảng services thành Collection và gom nhóm theo cấp độ (Basic, Premium, Extra)
                            $groupedServices = collect($services)->groupBy('service_level');
                        @endphp

                        <div class="space-y-8">
                            @foreach($groupedServices as $level => $items)
                                <div>
                                    <h4 class="text-sm font-serif text-dark mb-4 border-b border-gray-100 pb-2">
                                        {{ $level === 'Premium' ? 'Gói Cao Cấp (Premium)' : ($level === 'Basic' ? 'Gói Tiêu Chuẩn (Basic)' : 'Dịch vụ thêm') }}
                                    </h4>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        @foreach($items as $service)
                                            <div class="relative">
                                                {{-- Đổi name thành mảng (service_ids[]) để cho phép chọn nhiều --}}
                                                <input type="checkbox" id="service_{{ $service->id }}" name="service_ids[]" value="{{ $service->id }}" class="service-checkbox">
                                                <label for="service_{{ $service->id }}" class="service-label">
                                                    <i class="fas fa-check-circle check-icon mr-3"></i>
                                                    <span class="flex-1">{{ $service->name }}</span>
                                                    <span class="text-xs text-gold font-medium">{{ $service->price_text }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- LỜI NHẮN --}}
                    <div class="group pt-6">
                        <label class="form-label">Lời nhắn / Yêu cầu đặc biệt</label>
                        <textarea name="message" rows="3" class="form-input resize-none" placeholder="Hãy kể cho Thảo về địa điểm, trang phục hoặc mong muốn cụ thể của bạn..."></textarea>
                    </div>

                    {{-- NÚT GỬI --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full md:w-auto text-white px-12 py-4 rounded-full text-sm uppercase tracking-widest transition-all duration-500 shadow-xl hover:opacity-90" style="background-color: #3e2f2f;">
                            Gửi yêu cầu đặt lịch
                        </button>
                    </div>
                </form>

                {{-- THÔNG BÁO THÀNH CÔNG --}}
                @if(session('success'))
                <div class="absolute inset-0 bg-white/95 backdrop-blur-sm z-20 flex flex-col items-center justify-center text-center p-10">
                    <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-6 text-3xl">✓</div>
                    <h3 class="text-3xl font-serif mb-4" style="color: #3e2f2f;">Gửi thành công!</h3>
                    <p class="font-light text-gray-600 max-w-sm mb-6">{{ session('success') }}</p>
                    <a href="{{ route('booking.index') }}" class="uppercase tracking-widest text-xs font-bold border-b pb-1" style="color: #c8a98d; border-color: #c8a98d;">Gửi yêu cầu khác</a>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- BẢN ĐỒ --}}
    <section class="py-20 px-6" data-aos="fade-up">
        <div class="max-w-7xl mx-auto rounded-3xl overflow-hidden shadow-lg h-[450px] map-container">
            @if(isset($settings['map_iframe']))
                <iframe src="{{ $settings['map_iframe'] }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500">Chưa cập nhật bản đồ</div>
            @endif
        </div>
    </section>
@endsection