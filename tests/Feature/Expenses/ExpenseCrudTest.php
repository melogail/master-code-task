<?php

namespace Tests\Feature\Expenses;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createExpense()
    {
        $category = Category::factory()->create(['is_active' => true]);
        $vendor = Vendor::factory()->create(['is_active' => true]);

        return Expense::factory()->create([
            'category_id' => $category->id,
            'vendor_id' => $vendor->id,
        ]);
    }

    public function test_admin_can_create_expense()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['is_active' => true]);
        $vendor = Vendor::factory()->create(['is_active' => true]);

        $payload = [
            'category_id' => $category->id,
            'vendor_id' => $vendor->id,
            'amount' => 100.50,
            'date' => '2025-12-30',
            'description' => 'Test Expense',
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('expenses', [
            'amount' => $payload['amount'],
            'description' => $payload['description'],
        ]);
    }

    public function test_admin_can_update_expense()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();

        $payload = [
            'category_id' => $expense->category_id,
            'vendor_id' => $expense->vendor_id,
            'amount' => 200.00,
            'date' => '2025-12-30',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson('/api/expenses/' . $expense->id, $payload);

        $response->assertOk();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 200.00,
            'description' => 'Updated Description',
        ]);
    }

    public function test_admin_can_delete_expense()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();

        $response = $this->deleteJson('/api/expenses/' . $expense->id);

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Expense deleted successfully',
            ]);

        $this->assertSoftDeleted('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_admin_can_view_soft_deleted_expenses()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->getJson('/api/expenses/trashed');

        $response->assertOk();
    }

    public function test_admin_can_view_soft_deleted_expense()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->getJson('/api/expenses/' . $expense->id . '/trashed');

        $response->assertOk();
    }

    public function test_admin_can_restore_expense()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->postJson('/api/expenses/' . $expense->id . '/restore');

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Expense restored successfully',
            ]);

        $this->assertNotSoftDeleted('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_admin_can_force_delete_expense()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->deleteJson('/api/expenses/' . $expense->id . '/force-delete');

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Expense permanently deleted successfully',
            ]);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_admin_can_view_expense()
    {
        $this->actingAsAdmin();

        $expense = $this->createExpense();

        $response = $this->getJson('/api/expenses/' . $expense->id);

        $response->assertOk();
    }

    /**
     * ==================
     * == Staff Tests ===
     * ==================
     */

    public function test_staff_can_create_expense()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);
        $vendor = Vendor::factory()->create(['is_active' => true]);

        $payload = [
            'category_id' => $category->id,
            'vendor_id' => $vendor->id,
            'amount' => 100.50,
            'date' => '2025-12-30',
            'description' => 'Test Expense',
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('expenses', [
            'amount' => $payload['amount'],
            'description' => $payload['description'],
        ]);
    }

    public function test_staff_can_view_expense()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();

        $response = $this->getJson('/api/expenses/' . $expense->id);

        $response->assertOk();
    }

    public function test_staff_can_view_expenses()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();

        $response = $this->getJson('/api/expenses');

        $response->assertOk();
    }

    public function test_staff_can_delete_expense()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();

        $response = $this->deleteJson('/api/expenses/' . $expense->id);

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Expense deleted successfully',
            ]);

        $this->assertSoftDeleted('expenses', [
            'id' => $expense->id,
        ]);
    }
}
