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
        Schema::table('bookings', function (Blueprint $table) {
            // 1. Phải xóa khóa ngoại trước
            $table->dropForeign(['service_id']); 
            
            // 2. Sau đó mới xóa cột
            $table->dropColumn('service_id');
            
            // 3. Cuối cùng thêm cột mới
            $table->json('service_ids')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
