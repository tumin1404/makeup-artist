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
            if (Schema::hasColumn('bookings', 'status')) {
                $table->index('status', 'bookings_status_index');
            }
            if (Schema::hasColumn('bookings', 'booking_date')) {
                $table->index('booking_date', 'bookings_booking_date_index');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_date')) {
                $table->index('payment_date', 'payments_payment_date_index');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'expense_date')) {
                $table->index('expense_date', 'expenses_expense_date_index');
            }
            if (Schema::hasColumn('expenses', 'category')) {
                $table->index('category', 'expenses_category_index');
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'status')) {
                $table->index('status', 'posts_status_index');
            }
            if (Schema::hasColumn('posts', 'published_at')) {
                $table->index('published_at', 'posts_published_at_index');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'is_active')) {
                $table->index('is_active', 'services_is_active_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_index');
            $table->dropIndex('bookings_booking_date_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_payment_date_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_expense_date_index');
            $table->dropIndex('expenses_category_index');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_index');
            $table->dropIndex('posts_published_at_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_is_active_index');
        });
    }
};
