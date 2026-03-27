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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // Tên sản phẩm/Dịch vụ
            $table->string('category')->nullable(); // Phân loại: Mỹ phẩm, Dụng cụ, Khác...
            $table->integer('amount'); // Số tiền chi
            $table->date('expense_date'); // Ngày mua
            $table->string('product_image')->nullable(); // Ảnh sản phẩm để mua lại
            $table->string('receipt_image')->nullable(); // Ảnh hóa đơn/bill
            $table->string('buy_link')->nullable(); // Link mua hàng (Shopee, Facebook store...)
            $table->text('notes')->nullable(); // Ghi chú thêm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
