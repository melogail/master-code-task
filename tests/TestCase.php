<?php

namespace Tests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    protected function actingAsAdmin()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        return $admin;

    }

    protected function actingAsStaff()
    {
        $staff = User::factory()->staff()->create();
        Sanctum::actingAs($staff);
        return $staff;
    }
}
