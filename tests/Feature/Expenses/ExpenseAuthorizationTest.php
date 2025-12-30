<?php

namespace Tests\Feature\Expenses;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Expense;
use App\Models\Vendor;
class ExpenseAuthorizationTest extends TestCase
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


    public function test_staff_cannot_update_expense()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();

        $response = $this->putJson('/api/expenses/' . $expense->id, [
            'category_id' => $expense->category_id,
            'vendor_id' => $expense->vendor_id,
            'amount' => 100,
            'date' => $expense->date,
            'description' => $expense->description,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => $expense->amount,
        ]);
    }

    public function test_staff_cannot_restore_expense()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->postJson('/api/expenses/' . $expense->id . '/restore');

        $response->assertForbidden();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'deleted_at' => $expense->deleted_at,
        ]);
    }

    public function test_staff_cannot_permanently_delete_expense()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->deleteJson('/api/expenses/' . $expense->id . '/force-delete');

        $response->assertForbidden();

        $this->assertSoftDeleted('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_staff_cannot_view_soft_deleted_expenses()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->getJson('/api/expenses/trashed');

        $response->assertForbidden();
    }

    public function test_staff_cannot_view_soft_deleted_expense()
    {
        $this->actingAsStaff();

        $expense = $this->createExpense();
        $expense->delete();

        $response = $this->getJson('/api/expenses/' . $expense->id . '/trashed');

        $response->assertForbidden();
    }
}
