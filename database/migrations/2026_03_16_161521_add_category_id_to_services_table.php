<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Thêm cột category_id, cho phép null để không lỗi dữ liệu cũ
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};