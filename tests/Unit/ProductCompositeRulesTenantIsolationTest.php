<?php

namespace Tests\Unit;

use App\Models\Component;
use App\Models\Tenant;
use App\Models\User;
use App\Support\ProductCompositeRules;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductCompositeRulesTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_components_from_another_tenant(): void
    {
        $defaultTenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $otherTenant = Tenant::query()->create([
            'name' => 'Tenant C',
            'slug' => 'tenant-c',
            'is_active' => true,
            'recalculation_mode' => 'manual',
        ]);

        $this->actingAs(User::factory()->create([
            'tenant_id' => $defaultTenant->id,
            'is_super_admin' => false,
        ]));

        $localComponent = Component::query()->create([
            'tenant_id' => $defaultTenant->id,
            'name' => 'Componente local',
            'code' => 'LOC-01',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);
        $foreignComponent = Component::query()->create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Componente externo',
            'code' => 'EXT-02',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        ProductCompositeRules::assertValidComposite(
            ['currency' => 'UYU'],
            [
                ['component_id' => $localComponent->id, 'quantity' => 1],
                ['component_id' => $foreignComponent->id, 'quantity' => 1],
            ],
        );
    }
}
