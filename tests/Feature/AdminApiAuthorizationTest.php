<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_overview_requires_authentication(): void
    {
        $response = $this->getJson('/api/admin/overview');

        $response->assertUnauthorized();
    }

    public function test_admin_overview_forbids_non_admin_user(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($user)->getJson('/api/admin/overview');

        $response->assertForbidden();
    }

    public function test_admin_overview_allows_admin_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/admin/overview');

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'stats' => ['products', 'categories', 'orders', 'reviews', 'revenue'],
        ]);
    }
}
