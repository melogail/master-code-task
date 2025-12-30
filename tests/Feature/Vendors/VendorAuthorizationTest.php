<?php

namespace Tests\Feature\Vendors;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\Vendor;

class VendorAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_create_vendor()
    {
        $this->actingAsStaff();

        $payload = [
            'name' => 'Test Vendor',
            'email' => 'testvendor@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ];

        $response = $this->postJson('/api/vendors', $payload);

        $response->assertForbidden();

        $this->assertDatabaseEmpty('vendors');
    }

    public function test_staff_cannot_update_vendor()
    {
        $this->actingAsStaff();

        $vendor = Vendor::factory()->create(['is_active' => true]);

        $payload = [
            'name' => 'Updated Vendor',
            'email' => 'updatedvendor@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ];

        $response = $this->putJson('/api/vendors/' . $vendor->id, $payload);

        $response->assertForbidden();

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'name' => $vendor->name,
        ]);
    }

    public function test_staff_cannot_delete_vendor()
    {
        $this->actingAsStaff();

        $vendor = Vendor::factory()->create(['is_active' => true]);

        $response = $this->deleteJson('/api/vendors/' . $vendor->id);

        $response->assertForbidden();

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'name' => $vendor->name,
        ]);
    }

    public function test_staff_cannot_view_soft_deleted_vendors()
    {
        $this->actingAsStaff();

        $vendor = Vendor::factory()->create(['is_active' => true]);

        $vendor->delete();

        $response = $this->getJson('/api/vendors/trashed');

        $response->assertForbidden();
    }

    public function test_staff_cannot_view_soft_deleted_vendor()
    {
        $this->actingAsStaff();

        $vendor = Vendor::factory()->create(['is_active' => true]);

        $vendor->delete();

        $response = $this->getJson('/api/vendors/' . $vendor->id . '/trashed');

        $response->assertForbidden();
    }

    public function test_staff_cannot_restore_soft_deleted_vendor()
    {
        $this->actingAsStaff();

        $vendor = Vendor::factory()->create(['is_active' => true]);

        $vendor->delete();

        $response = $this->postJson('/api/vendors/' . $vendor->id . '/restore');

        $response->assertForbidden();

        $this->assertSoftDeleted('vendors', [
            'id' => $vendor->id,
            'name' => $vendor->name,
        ]);
    }

    public function test_staff_cannot_permanently_delete_soft_deleted_vendor()
    {
        $this->actingAsStaff();

        $vendor = Vendor::factory()->create(['is_active' => true]);

        $vendor->delete();

        $response = $this->deleteJson('/api/vendors/' . $vendor->id . '/force-delete');

        $response->assertForbidden();

        $this->assertSoftDeleted('vendors', [
            'id' => $vendor->id,
            'name' => $vendor->name,
        ]);
    }
}
