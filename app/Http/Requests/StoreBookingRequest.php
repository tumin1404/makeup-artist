<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'social_link' => ['nullable', 'string', 'max:255'],
            'booking_date' => ['required', 'date'],
            'service_ids'   => ['required', 'array', 'min:1'],
            'service_ids.*' => ['exists:services,id'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'customer_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.max' => 'Số điện thoại không hợp lệ.',
            'booking_date.required' => 'Vui lòng chọn ngày và giờ hẹn dự kiến.',
            'booking_date.date' => 'Ngày hẹn không đúng định dạng.',
            'service_ids.required' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'service_ids.array' => 'Dịch vụ đã chọn không hợp lệ.',
            'service_ids.min' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'service_ids.*.exists' => 'Một trong các dịch vụ đã chọn không tồn tại.',
            'message.max' => 'Lời nhắn không được vượt quá 2000 ký tự.',
        ];
    }
}
