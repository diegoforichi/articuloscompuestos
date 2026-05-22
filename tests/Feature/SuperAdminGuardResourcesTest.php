<?php

namespace Tests\Feature;

use App\Filament\Resources\IntegrationSettings\IntegrationSettingResource;
use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminGuardResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_super_admin_cannot_access_superadmin_resources(): void
    {
        $tenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertFalse(TenantResource::canViewAny());
        $this->assertFalse(IntegrationSettingResource::canViewAny());
    }

    public function test_super_admin_can_access_superadmin_resources(): void
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'is_super_admin' => true,
        ]);

        $this->actingAs($user);

        $this->assertTrue(TenantResource::canViewAny());
        $this->assertTrue(IntegrationSettingResource::canViewAny());
    }
}
