<?php

namespace Tests\Feature;

use App\Models\Component;
use App\Support\ProductCompositeRules;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductCompositeRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_when_less_than_two_components(): void
    {
        $c1 = Component::query()->create([
            'name' => 'Oro',
            'code' => 'ORO-1',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1_000,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        ProductCompositeRules::assertValidComposite([
            'currency' => 'UYU',
            'productComponents' => [
                ['component_id' => $c1->id, 'quantity' => 1],
            ],
        ]);
    }

    public function test_rejects_currency_mismatch(): void
    {
        $c1 = Component::query()->create([
            'name' => 'Oro',
            'code' => 'ORO-2',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1_000,
            'is_active' => true,
        ]);

        $c2 = Component::query()->create([
            'name' => 'Plata',
            'code' => 'PLA-2',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'USD',
            'current_unit_price_minor' => 500,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        ProductCompositeRules::assertValidComposite([
            'currency' => 'UYU',
            'productComponents' => [
                ['component_id' => $c1->id, 'quantity' => 1],
                ['component_id' => $c2->id, 'quantity' => 1],
            ],
        ]);
    }

    public function test_accepts_two_components_with_matching_currency(): void
    {
        $c1 = Component::query()->create([
            'name' => 'Oro',
            'code' => 'ORO-3',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1_000,
            'is_active' => true,
        ]);

        $c2 = Component::query()->create([
            'name' => 'Plata',
            'code' => 'PLA-3',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 500,
            'is_active' => true,
        ]);

        ProductCompositeRules::assertValidComposite([
            'currency' => 'UYU',
            'productComponents' => [
                ['component_id' => $c1->id, 'quantity' => 1],
                ['component_id' => $c2->id, 'quantity' => 1],
            ],
        ]);

        $this->assertTrue(true);
    }

    public function test_accepts_two_components_via_raw_rows_when_dehydrated_data_omits_repeater(): void
    {
        $c1 = Component::query()->create([
            'name' => 'Oro',
            'code' => 'ORO-4',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1_000,
            'is_active' => true,
        ]);

        $c2 = Component::query()->create([
            'name' => 'Plata',
            'code' => 'PLA-4',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 500,
            'is_active' => true,
        ]);

        ProductCompositeRules::assertValidComposite(
            ['currency' => 'UYU'],
            [
                'uuid-a' => ['component_id' => $c1->id, 'quantity' => 1],
                'uuid-b' => ['component_id' => $c2->id, 'quantity' => 1],
            ],
        );

        $this->assertTrue(true);
    }
}
