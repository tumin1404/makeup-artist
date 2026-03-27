<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('title'); // Tiêu đề: Cọc giữ lịch, Thanh toán nốt...
            $table->integer('amount'); // Số tiền
            $table->string('payment_method')->nullable(); // Tiền mặt, Chuyển khoản
            $table->string('proof_image')->nullable(); // Ảnh bill CK hoặc biên nhận
            $table->dateTime('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
