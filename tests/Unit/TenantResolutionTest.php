<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_default_tenant_id_uses_slug_default(): void
    {
        $defaultTenant = Tenant::query()->where('slug', Tenant::DEFAULT_SLUG)->firstOrFail();

        $this->assertSame((int) $defaultTenant->id, Tenant::resolveDefaultTenantId());
    }

    public function test_resolve_current_tenant_id_uses_user_tenant_when_present(): void
    {
        $defaultTenant = Tenant::query()->where('slug', Tenant::DEFAULT_SLUG)->firstOrFail();
        $otherTenant = Tenant::query()->create([
            'name' => 'Otro',
            'slug' => 'otro',
            'is_active' => true,
            'recalculation_mode' => 'manual',
        ]);

        $user = User::factory()->create([
            'tenant_id' => $otherTenant->id,
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertSame((int) $otherTenant->id, Tenant::resolveCurrentTenantId());
        $this->assertNotSame((int) $defaultTenant->id, Tenant::resolveCurrentTenantId());
    }

    public function test_superadmin_without_tenant_uses_default_tenant(): void
    {
        $defaultTenant = Tenant::query()->where('slug', Tenant::DEFAULT_SLUG)->firstOrFail();

        $user = User::factory()->create([
            'tenant_id' => null,
            'is_super_admin' => true,
        ]);

        $this->actingAs($user);

        $this->assertSame((int) $defaultTenant->id, Tenant::resolveCurrentTenantId());
    }

    public function test_inactive_user_tenant_returns_null_for_current_context(): void
    {
        $inactiveTenant = Tenant::query()->create([
            'name' => 'Inactivo',
            'slug' => 'inactivo',
            'is_active' => false,
            'recalculation_mode' => 'manual',
        ]);

        $user = User::factory()->create([
            'tenant_id' => $inactiveTenant->id,
            'is_super_admin' => false,
        ]);

        $this->actingAs($user);

        $this->assertNull(Tenant::resolveCurrentTenantId());
    }
}
