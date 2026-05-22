<?php

namespace Tests\Feature;

use App\Models\Component;
use App\Models\ComponentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentTypeCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_seeds_default_component_types(): void
    {
        $this->assertSame(4, ComponentType::query()->count());
        $this->assertNotNull(ComponentType::query()->where('code', 'metal')->first());
        $this->assertNotNull(ComponentType::query()->where('code', 'other')->first());
    }

    public function test_component_requires_component_type(): void
    {
        $typeId = $this->metalComponentTypeId();

        $component = Component::query()->create([
            'name' => 'Harina 000',
            'code' => 'HAR-1',
            'component_type_id' => $typeId,
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 500,
            'is_active' => true,
        ]);

        $this->assertSame($typeId, $component->component_type_id);
        $this->assertSame('metal', $component->componentType->code);
    }
}
