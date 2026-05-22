<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_panel(): void
    {
        $panel = $this->createMock(Panel::class);
        $user = User::factory()->create([
            'tenant_id' => null,
            'is_super_admin' => true,
        ]);

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_tenant_admin_with_active_tenant_can_access_panel(): void
    {
        $panel = $this->createMock(Panel::class);
        $tenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_super_admin' => false,
        ]);

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_tenant_admin_with_inactive_tenant_cannot_access_panel(): void
    {
        $panel = $this->createMock(Panel::class);
        $tenant = Tenant::query()->create([
            'name' => 'Inactivo',
            'slug' => 'inactivo',
            'is_active' => false,
            'recalculation_mode' => 'manual',
        ]);
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_super_admin' => false,
        ]);

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_user_without_tenant_cannot_access_panel_if_not_superadmin(): void
    {
        $panel = $this->createMock(Panel::class);
        $user = User::factory()->create([
            'tenant_id' => null,
            'is_super_admin' => false,
        ]);

        $this->assertFalse($user->canAccessPanel($panel));
    }
}
