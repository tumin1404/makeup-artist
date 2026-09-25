<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\User;
use App\Policies\ExpensePolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class FinancialAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_permission_cannot_view_or_create_financial_records(): void
    {
        Permission::create(['name' => 'view_any_expense', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_expense', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_expense', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_any_payment', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_payment', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_payment', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $policyExpense = new ExpensePolicy();
        $policyPayment = new PaymentPolicy();

        $booking = Booking::create([
            'customer_name' => 'Khách Test',
            'phone' => '0912345678',
            'booking_date' => now(),
            'status' => 'confirmed',
            'total_amount' => 1000000,
        ]);

        $expense = Expense::create([
            'item_name' => 'Son',
            'amount' => 100000,
            'category' => 'Mỹ phẩm',
            'expense_date' => now(),
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'title' => 'Cọc',
            'amount' => 200000,
            'payment_method' => 'Tiền mặt',
            'payment_date' => now(),
        ]);

        $this->assertFalse($policyExpense->viewAny($user));
        $this->assertFalse($policyExpense->view($user, $expense));
        $this->assertFalse($policyExpense->create($user));

        $this->assertFalse($policyPayment->viewAny($user));
        $this->assertFalse($policyPayment->view($user, $payment));
        $this->assertFalse($policyPayment->create($user));
    }

    public function test_user_with_permission_can_view_and_create_financial_records(): void
    {
        Permission::create(['name' => 'view_any_expense', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_expense', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_any_payment', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_payment', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view_any_expense', 'create_expense', 'view_any_payment', 'create_payment']);

        $policyExpense = new ExpensePolicy();
        $policyPayment = new PaymentPolicy();

        $this->assertTrue($policyExpense->viewAny($user));
        $this->assertTrue($policyExpense->create($user));
        $this->assertTrue($policyPayment->viewAny($user));
        $this->assertTrue($policyPayment->create($user));
    }
}
