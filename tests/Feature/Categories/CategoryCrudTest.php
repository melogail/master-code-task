<?php

namespace Tests\Feature\Categories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Expense;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_category()
    {
        $this->actingAsAdmin();

        $payload = ['name' => 'New Category'];

        $response = $this->postJson('/api/categories', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('categories', [
            'name' => $payload['name'],
            'deleted_at' => null
        ]);
    }

    public function test_admin_can_update_a_category()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['is_active' => true]);

        $payload = ['name' => 'Updated Category'];

        $response = $this->putJson('/api/categories/' . $category->id, $payload);

        $response->assertOk();

        $this->assertDatabaseHas('categories', [
            'name' => $payload['name'],
            'deleted_at' => null
        ]);
    }

    public function test_admin_can_delete_a_category()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['is_active' => true]);

        $response = $this->deleteJson('/api/categories/' . $category->id);
        $category->refresh();

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Category deleted successfully'
            ]);

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
            'deleted_at' => $category->deleted_at
        ]);
    }

    public function test_admin_can_restore_soft_deleted_category()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->postJson('/api/categories/' . $category->id . '/restore');

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Category restored successfully'
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null
        ]);
    }

    public function test_admin_can_permanently_delete_soft_deleted_category()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->deleteJson('/api/categories/' . $category->id . '/force-delete');

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Category deleted permanently successfully'
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    public function test_admin_cannot_delete_category_with_expenses()
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create(['is_active' => true]);

        Expense::factory()->create([
            'category_id' => $category->id
        ]);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(400)
            ->assertJsonFragment([
                'message' => 'Category has expenses'
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null
        ]);
    }

    /**
     * ===========================
     * == STAFF TESTS
     * ===========================
     */

    public function test_staff_cannot_create_a_category()
    {
        $this->actingAsStaff();

        $payload = ['name' => 'New Category'];

        $response = $this->postJson('/api/categories', $payload);

        $response->assertForbidden();

        $this->assertDatabaseEmpty('categories');
    }

    public function test_staff_cannot_update_a_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $payload = ['name' => 'Updated Category'];

        $response = $this->putJson('/api/categories/' . $category->id, $payload);

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name
        ]);
    }

    public function test_staff_cannot_delete_a_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null
        ]);
    }

    public function test_staff_cannot_restore_soft_deleted_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->postJson('/api/categories/' . $category->id . '/restore');

        $response->assertForbidden();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
            'deleted_at' => $category->deleted_at
        ]);
    }

    public function test_staff_cannot_permanently_delete_soft_deleted_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->deleteJson('/api/categories/' . $category->id . '/force-delete');

        $response->assertForbidden();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
            'deleted_at' => $category->deleted_at
        ]);
    }

    public function test_staff_cannot_view_trashed_categories()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->getJson('/api/categories/trashed');

        $response->assertForbidden();
    }

    public function test_staff_cannot_view_trashed_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->getJson('/api/categories/' . $category->id . '/trashed');

        $response->assertForbidden();
    }

    public function test_staff_can_view_categories()
    {
        $this->actingAsStaff();

        Category::factory()->count(5)->create(['is_active' => true]);

        $response = $this->getJson('/api/categories');

        $response->assertOk();
    }
}
