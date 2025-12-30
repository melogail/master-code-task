<?php

namespace Tests\Feature\Categories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;

class CategoryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_categories()
    {
        $this->actingAsStaff();

        Category::factory()->count(5)->create(['is_active' => true]);

        $response = $this->getJson('/api/categories');

        $response->assertOk();
    }

    public function test_staff_cannot_create_category()
    {
        $this->actingAsStaff();

        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_staff_cannot_update_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $response = $this->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Category',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function test_staff_cannot_delete_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    public function test_staff_cannot_view_soft_deleted_categories()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->getJson('/api/categories/trashed');

        $response->assertForbidden();
    }

    public function test_staff_cannot_view_soft_deleted_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->getJson('/api/categories/' . $category->id . '/trashed');

        $response->assertForbidden();
    }

    public function test_staff_cannot_restore_soft_deleted_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->postJson('/api/categories/' . $category->id . '/restore');

        $response->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => $category->deleted_at,
        ]);
    }

    public function test_staff_cannot_permanently_delete_soft_deleted_category()
    {
        $this->actingAsStaff();

        $category = Category::factory()->create(['is_active' => true]);

        $category->delete();

        $response = $this->deleteJson('/api/categories/' . $category->id . '/force-delete');

        $response->assertForbidden();
    }
}
