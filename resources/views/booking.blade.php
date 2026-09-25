@extends('layouts.app')

@section('title', 'Liên Hệ & Đặt Lịch | ' . ($settings['site_name'] ?? ''))

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            border-bottom-color: var(--color-gold);
            padding-left: 8px;
        }
        .form-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--color-gold);
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
            color: var(--color-dark);
        }
        .service-checkbox:checked + .service-label {
            border-color: var(--color-gold);
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
            color: var(--color-gold);
        }

        /* Flatpickr Luxury Theme */
        .flatpickr-calendar {
            background: #fdfbf9 !important;
            border: 1px solid rgba(200, 169, 141, 0.35) !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 45px -10px rgba(62, 47, 47, 0.18) !important;
            font-family: inherit !important;
            padding: 12px 10px !important;
            width: 328px !important;
            box-sizing: border-box !important;
            overflow: visible !important;
        }
        .flatpickr-calendar.arrowTop:before,
        .flatpickr-calendar.arrowTop:after {
            border-bottom-color: #fdfbf9 !important;
        }
        .flatpickr-calendar.arrowBottom:before,
        .flatpickr-calendar.arrowBottom:after {
            border-top-color: #fdfbf9 !important;
        }
        .flatpickr-innerContainer {
            width: 100% !important;
            display: block !important;
            overflow: visible !important;
            box-sizing: border-box !important;
        }
        .flatpickr-rContainer {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
            box-sizing: border-box !important;
        }
        .flatpickr-months {
            border-bottom: 1px solid rgba(200, 169, 141, 0.18) !important;
            padding: 2px 0 8px 0 !important;
            margin-bottom: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            position: relative !important;
        }
        .flatpickr-months .flatpickr-month {
            color: var(--color-dark) !important;
            fill: var(--color-dark) !important;
            height: auto !important;
            line-height: 1 !important;
            flex: 1 !important;
        }
        .flatpickr-current-month {
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            color: var(--color-dark) !important;
            padding: 0 !important;
            height: auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
            position: static !important;
            width: 100% !important;
        }
        .flatpickr-current-month .cur-month {
            font-weight: 600 !important;
            margin-left: 0 !important;
        }
        .flatpickr-current-month select.flatpickr-monthDropdown-months {
            font-weight: 600 !important;
            color: var(--color-dark) !important;
            background: transparent !important;
            border: none !important;
            padding: 2px 6px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
        }
        .flatpickr-current-month .numInputWrapper {
            width: 60px !important;
        }
        .flatpickr-current-month input.cur-year {
            font-weight: 600 !important;
            color: var(--color-dark) !important;
            padding: 2px 0 !important;
        }
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            color: var(--color-dark) !important;
            fill: var(--color-dark) !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            position: static !important;
            height: auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            color: var(--color-gold) !important;
            background: rgba(200, 169, 141, 0.15) !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover svg,
        .flatpickr-months .flatpickr-next-month:hover svg {
            fill: var(--color-gold) !important;
        }
        .flatpickr-weekdays {
            width: 100% !important;
            max-width: 100% !important;
            margin-top: 4px !important;
            margin-bottom: 4px !important;
            display: flex !important;
            align-items: center !important;
            height: auto !important;
            box-sizing: border-box !important;
        }
        .flatpickr-weekdaycontainer {
            width: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
        }
        span.flatpickr-weekday {
            flex: 1 1 0% !important;
            text-align: center !important;
            color: var(--color-gold) !important;
            font-weight: 600 !important;
            font-size: 0.72rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            line-height: 1.5 !important;
        }
        .flatpickr-days {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
            overflow: visible !important;
            box-sizing: border-box !important;
        }
        .dayContainer {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-start !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
        .flatpickr-day {
            flex: 0 0 14.2857% !important;
            width: 14.2857% !important;
            max-width: 14.2857% !important;
            height: 38px !important;
            line-height: 38px !important;
            margin: 2px 0 !important;
            border-radius: 10px !important;
            color: var(--color-dark) !important;
            font-weight: 400 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            box-sizing: border-box !important;
        }
        .flatpickr-day:hover {
            background: rgba(200, 169, 141, 0.2) !important;
            border-color: transparent !important;
        }
        .flatpickr-day.today {
            border-color: var(--color-gold) !important;
            color: var(--color-gold) !important;
            font-weight: 600 !important;
        }
        .flatpickr-day.today:hover {
            background: rgba(200, 169, 141, 0.25) !important;
            color: var(--color-dark) !important;
        }
        .flatpickr-day.selected,
        .flatpickr-day.selected:focus,
        .flatpickr-day.selected:hover {
            background: var(--color-button) !important;
            border-color: var(--color-button) !important;
            color: var(--color-button-text) !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(62, 47, 47, 0.25) !important;
        }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #d1d5db !important;
            cursor: not-allowed !important;
            background: transparent !important;
        }
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #c8b9a6 !important;
            opacity: 0.6;
        }
    </style>
@endsection

@section('content')
    <section class="pt-40 pb-20 px-6 max-w-7xl mx-auto">
        <div class="text-center md:text-left mb-20" data-aos="fade-up">
            <span class="text-gold text-sm tracking-widest uppercase mb-2 block">{{ $settings['booking_pretitle'] ?? 'Connect with us' }}</span>
            <h1 class="text-5xl md:text-7xl font-serif text-dark leading-tight">
                {!! $settings['booking_title'] ?? 'Gửi Lời Nhắn <br> <span class="italic font-light">Tư Vấn & Đặt Lịch</span>' !!}
            </h1>
        </div>

        <div class="flex flex-col lg:flex-row gap-20">
            {{-- THÔNG TIN LIÊN HỆ (BÊN TRÁI) --}}
            <div class="lg:w-4/12 space-y-12" data-aos="fade-right" data-aos-delay="200">
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-bold mb-6 text-gold">Thông tin liên lạc</h3>
                    <div class="space-y-4 font-light text-lg text-gray-800">
                        @if(!empty($settings['hotline']))
                            <p class="hover:text-gold transition-colors">
                                <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}">
                                    <i class="fas fa-phone-alt text-sm text-gold mr-2"></i> {{ $settings['hotline'] }}
                                </a>
                            </p>
                        @endif
                        @if(!empty($settings['email']))
                            <p class="hover:text-gold transition-colors">
                                <a href="mailto:{{ $settings['email'] }}">
                                    <i class="fas fa-envelope text-sm text-gold mr-2"></i> {{ $settings['email'] }}
                                </a>
                            </p>
                        @endif
                        @if(!empty($settings['social_zalo']))
                            <p class="hover:text-gold transition-colors">
                                <a href="{{ $settings['social_zalo'] }}" target="_blank">
                                    <i class="fas fa-comment-dots text-sm text-gold mr-2"></i> Nhắn tin Zalo tư vấn
                                </a>
                            </p>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-bold mb-6 text-gold">Địa điểm & Thời gian</h3>
                    <div class="space-y-4 font-light text-base text-gray-800">
                        @if(!empty($settings['address_main']))
                            <p><strong class="font-medium text-dark">Cơ sở 1:</strong> {{ $settings['address_main'] }}</p>
                        @endif
                        @if(!empty($settings['address_branch']))
                            <p><strong class="font-medium text-dark">Cơ sở 2:</strong> {{ $settings['address_branch'] }}</p>
                        @endif
                        @if(empty($settings['address_main']) && empty($settings['address_branch']) && !empty($settings['address']))
                            <p>{{ $settings['address'] }}</p>
                        @endif
                        @if(!empty($settings['working_hours']))
                            <p class="text-sm text-gray-600"><i class="far fa-clock text-gold mr-1.5"></i> {{ $settings['working_hours'] }}</p>
                        @endif
                        <p class="text-xs italic text-gray-500 mt-2">{{ $settings['booking_coverage_note'] ?? '* Nhận booking phục vụ tận nơi cho cô dâu & sự kiện.' }}</p>
                    </div>
                </div>
            </div>

            {{-- FORM ĐẶT LỊCH (BÊN PHẢI) --}}
            <div class="lg:w-8/12 bg-white p-10 md:p-16 rounded-2xl shadow-sm relative overflow-hidden" data-aos="fade-left" data-aos-delay="400">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary rounded-bl-full opacity-50"></div>
                
                <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-10 relative z-10">
                    @csrf
                    
                    {{-- THÔNG TIN CÁ NHÂN & NGÀY HẸN --}}
                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="group">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" name="customer_name" class="form-input" placeholder="VD: Nguyễn Văn A" required>
                        </div>
                        <div class="group">
                            <label class="form-label">Số điện thoại / Zalo (Để tư vấn) *</label>
                            <input type="tel" name="phone" class="form-input" placeholder="VD: 090 123 4567" required>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="group">
                            <label class="form-label">Ngày bắt đầu dự kiến *</label>
                            <div class="relative">
                                <input type="text" id="booking_date_picker" name="booking_date" class="form-input cursor-pointer pr-8" placeholder="Chọn ngày bắt đầu (VD: 25/10/2026)" required readonly>
                                <i class="far fa-calendar-alt absolute right-2 top-1/2 -translate-y-1/2 text-gold pointer-events-none text-base"></i>
                            </div>
                            <p class="text-xs text-gray-400 mt-2 italic">* {{ $settings['booking_date_note'] ?? 'Nếu sự kiện diễn ra trong nhiều ngày (VD: Ăn hỏi + Cưới), vui lòng chọn ngày khởi đầu. Chúng tôi sẽ chốt chi tiết lịch trình sau khi tư vấn.' }}</p>
                        </div>
                        <div class="group">
                            <label class="form-label">Link Facebook / Instagram</label>
                            <input type="text" name="social_link" class="form-input" placeholder="{{ $settings['booking_placeholder_social'] ?? 'Link trang cá nhân để xem phong cách của bạn' }}">
                        </div>
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
                        <textarea name="message" rows="3" class="form-input resize-none" placeholder="{{ $settings['booking_placeholder_message'] ?? 'Hãy chia sẻ về địa điểm, phong cách mong muốn hoặc yêu cầu cụ thể của bạn...' }}"></textarea>
                    </div>

                    {{-- NÚT GỬI --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full md:w-auto btn-theme-primary px-12 py-4 rounded-full text-sm uppercase tracking-widest transition-all duration-500 shadow-xl hover:opacity-90 flex items-center justify-center">
                            <span>Gửi yêu cầu đặt lịch</span>
                        </button>
                    </div>
                </form>
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

    {{-- POPUP MODAL THÔNG BÁO ĐẶT LỊCH (THÀNH CÔNG / LỖI) --}}
    <div id="bookingModal" class="fixed inset-0 z-[100] items-center justify-center p-4 sm:p-6 hidden opacity-0 transition-opacity duration-300">
        {{-- Backdrop mờ sang trọng --}}
        <div id="bookingModalBackdrop" class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
        
        {{-- Khối hộp thông báo nổi --}}
        <div id="bookingModalCard" class="bg-white rounded-3xl max-w-lg w-full shadow-2xl p-8 md:p-10 relative z-10 transform scale-95 transition-all duration-300 border border-gold/20 text-center">
            {{-- NỘI DUNG THÀNH CÔNG --}}
            <div id="modalSuccessContent">
                <div class="w-20 h-20 bg-emerald-50 text-emerald-500 border border-emerald-200 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
                    <i class="fas fa-check"></i>
                </div>
                
                <span class="text-xs uppercase tracking-[0.25em] font-bold block mb-2 text-gold">Booking Confirmed</span>
                <h3 class="text-3xl md:text-4xl font-serif font-bold mb-4 text-dark">Đặt Lịch Thành Công!</h3>
                
                <p id="modalSuccessMsg" class="font-light text-gray-600 text-base leading-relaxed mb-6">
                    {{ $settings['booking_success_message'] ?? 'Cảm ơn quý khách! Chúng tôi đã nhận được thông tin và sẽ liên hệ lại với bạn sớm nhất.' }}
                </p>

                {{-- Thẻ tóm tắt thông tin vừa đặt --}}
                <div id="modalBookingSummary" class="bg-[#fcfaf7] border border-gold/30 rounded-2xl p-6 md:p-7 mb-7 text-left hidden shadow-sm divide-y divide-gold/20">
                    <div class="flex items-center justify-between py-3.5 first:pt-0">
                        <span class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Khách hàng:</span>
                        <span id="summaryCustomerName" class="font-bold text-dark text-base break-words text-right"></span>
                    </div>
                    <div class="flex items-center justify-between py-3.5">
                        <span class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Số điện thoại:</span>
                        <span id="summaryPhone" class="font-semibold text-dark text-base text-right"></span>
                    </div>
                    <div class="flex items-center justify-between py-3.5 last:pb-0">
                        <span class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Ngày hẹn:</span>
                        <span id="summaryDate" class="font-semibold text-gold text-base text-right"></span>
                    </div>
                </div>

                @if(!empty($settings['hotline']))
                <p class="text-xs text-gray-500 mt-8 mb-8 italic">
                    Cần hỗ trợ trực tiếp? Gọi Hotline: <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}" class="font-semibold hover:underline text-gold">{{ $settings['hotline'] }}</a>
                </p>
                @endif

                <div class="flex justify-center mt-8">
                    <button type="button" onclick="closeBookingModal()" class="w-full max-w-[260px] btn-theme-primary py-4 rounded-full text-xs uppercase tracking-widest transition-all duration-300 shadow-lg hover:shadow-xl hover:opacity-90 font-medium cursor-pointer inline-flex items-center justify-center">
                        Đóng
                    </button>
                </div>
            </div>

            {{-- NỘI DUNG LỖI --}}
            <div id="modalErrorContent" class="hidden">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 border border-rose-200 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
                    <i class="fas fa-exclamation"></i>
                </div>
                
                <span class="text-xs uppercase tracking-[0.25em] font-bold block mb-2 text-rose-500">Notice</span>
                <h3 class="text-2xl md:text-3xl font-serif font-bold mb-4" style="color: #3e2f2f;">Chưa Thể Gửi Yêu Cầu</h3>
                
                <div id="modalErrorList" class="bg-rose-50/70 border border-rose-200/60 rounded-2xl p-5 mb-6 text-left space-y-2 text-sm text-rose-700">
                    <!-- Errors populated by JS -->
                </div>

                <div class="flex justify-center mt-8">
                    <button type="button" onclick="closeBookingModal()" class="w-full max-w-[260px] text-white py-4 rounded-full text-xs uppercase tracking-widest transition-all duration-300 shadow-lg hover:shadow-xl hover:opacity-90 font-medium cursor-pointer inline-flex items-center justify-center" style="background-color: #3e2f2f;">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script>
        // Khởi tạo Flatpickr Luxury Theme với ngôn ngữ Tiếng Việt
        const fpDate = flatpickr("#booking_date_picker", {
            locale: "vn",
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            disableMobile: true,
            altInputClass: "form-input cursor-pointer font-light text-dark pr-8",
            prevArrow: '<i class="fas fa-chevron-left text-xs"></i>',
            nextArrow: '<i class="fas fa-chevron-right text-xs"></i>',
        });

        const bookingForm = document.getElementById('bookingForm');
        const bookingModal = document.getElementById('bookingModal');
        const bookingModalCard = document.getElementById('bookingModalCard');
        const modalSuccessContent = document.getElementById('modalSuccessContent');
        const modalErrorContent = document.getElementById('modalErrorContent');
        const modalSuccessMsg = document.getElementById('modalSuccessMsg');
        const modalBookingSummary = document.getElementById('modalBookingSummary');
        const summaryCustomerName = document.getElementById('summaryCustomerName');
        const summaryPhone = document.getElementById('summaryPhone');
        const summaryDate = document.getElementById('summaryDate');
        const modalErrorList = document.getElementById('modalErrorList');
        const submitBtn = bookingForm ? bookingForm.querySelector('button[type="submit"]') : null;

        function formatBookingDate(val) {
            if (!val) return '';
            if (val.includes('/')) return val; // already formatted dd/mm/yyyy
            const clean = val.split('T')[0].split(' ')[0];
            const parts = clean.split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
            return val;
        }

        function openBookingModal(type = 'success', data = {}) {
            if (!bookingModal) return;

            if (type === 'success') {
                modalSuccessContent.classList.remove('hidden');
                modalErrorContent.classList.add('hidden');
                
                if (data.message) {
                    modalSuccessMsg.textContent = data.message;
                }
                if (data.customer_name || data.phone || data.booking_date) {
                    modalBookingSummary.classList.remove('hidden');
                    summaryCustomerName.textContent = data.customer_name || '';
                    summaryPhone.textContent = data.phone || '';
                    summaryDate.textContent = formatBookingDate(data.booking_date || '');
                } else {
                    modalBookingSummary.classList.add('hidden');
                }
            } else {
                modalSuccessContent.classList.add('hidden');
                modalErrorContent.classList.remove('hidden');

                let errorHtml = '';
                if (Array.isArray(data.errors) && data.errors.length > 0) {
                    errorHtml = '<ul class="list-disc list-inside space-y-1.5">';
                    data.errors.forEach(err => {
                        errorHtml += `<li>${err}</li>`;
                    });
                    errorHtml += '</ul>';
                } else {
                    errorHtml = `<p>${data.message || 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại thông tin.'}</p>`;
                }
                modalErrorList.innerHTML = errorHtml;
            }

            document.body.style.overflow = 'hidden';
            bookingModal.classList.remove('hidden');
            bookingModal.classList.add('flex');
            setTimeout(() => {
                bookingModal.classList.remove('opacity-0');
                bookingModalCard.classList.remove('scale-95');
                bookingModalCard.classList.add('scale-100');
            }, 20);
        }

        function closeBookingModal() {
            if (!bookingModal) return;
            bookingModal.classList.add('opacity-0');
            bookingModalCard.classList.remove('scale-100');
            bookingModalCard.classList.add('scale-95');
            setTimeout(() => {
                bookingModal.classList.add('hidden');
                bookingModal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        }

        // Backdrop click and ESC key
        if (bookingModal) {
            document.getElementById('bookingModalBackdrop')?.addEventListener('click', closeBookingModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !bookingModal.classList.contains('hidden')) {
                    closeBookingModal();
                }
            });
        }

        // AJAX Submission handler
        if (bookingForm) {
            bookingForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Validate service checkboxes
                const checkedServices = bookingForm.querySelectorAll('input[name="service_ids[]"]:checked');
                if (checkedServices.length === 0) {
                    openBookingModal('error', {
                        errors: ['Vui lòng chọn ít nhất một dịch vụ bạn quan tâm.']
                    });
                    return;
                }

                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> <span>Đang gửi yêu cầu...</span>';
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                }

                try {
                    const formData = new FormData(bookingForm);
                    const response = await fetch(bookingForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        bookingForm.reset();
                        if (typeof fpDate !== 'undefined' && fpDate) {
                            fpDate.clear();
                        }
                        openBookingModal('success', {
                            message: data.message,
                            customer_name: data.data?.customer_name,
                            phone: data.data?.phone,
                            booking_date: data.data?.booking_date,
                        });
                    } else if (response.status === 422) {
                        const errorMessages = [];
                        if (data.errors) {
                            Object.values(data.errors).forEach(errArray => {
                                if (Array.isArray(errArray)) {
                                    errArray.forEach(err => errorMessages.push(err));
                                } else {
                                    errorMessages.push(errArray);
                                }
                            });
                        }
                        openBookingModal('error', {
                            message: data.message || 'Thông tin chưa đầy đủ hoặc không hợp lệ.',
                            errors: errorMessages.length > 0 ? errorMessages : [data.message]
                        });
                    } else {
                        openBookingModal('error', {
                            message: data.message || 'Không thể gửi yêu cầu vào lúc này. Vui lòng liên hệ trực tiếp qua Hotline.'
                        });
                    }
                } catch (err) {
                    openBookingModal('error', {
                        message: 'Lỗi kết nối mạng. Quý khách vui lòng kiểm tra kết nối hoặc liên hệ qua Hotline/Zalo.'
                    });
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    }
                }
            });
        }

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => {
                openBookingModal('success', {
                    message: @json(session('success'))
                });
            });
        @endif
    </script>
@endsection