<?php

namespace Tests\Feature;

use App\Models\Component;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRecalculatePriceSyncStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_recalculate_sets_draft_when_no_external_id_and_sync_status_was_null_in_memory(): void
    {
        $c1 = Component::query()->create([
            'name' => 'Oro',
            'code' => 'ORO-R',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1_000,
            'is_active' => true,
        ]);

        $c2 = Component::query()->create([
            'name' => 'Plata',
            'code' => 'PLA-R',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 500,
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Anillo',
            'code' => 'PR-R1',
            'description' => null,
            'category_name' => 'Cat',
            'currency' => 'UYU',
            'cost_minor' => 0,
            'margin_percent' => 0,
            'price_minor' => 0,
            'ind_fact_id' => 3,
            'article_type_id' => 1,
            'external_id' => null,
            'sync_status' => 'draft',
            'last_synced_at' => null,
        ]);

        $product->components()->attach([
            $c1->id => ['quantity' => 1, 'sort_order' => 0],
            $c2->id => ['quantity' => 1, 'sort_order' => 1],
        ]);

        $product->load('components');
        $product->sync_status = null;

        $product->recalculatePrice();

        $product->refresh();

        $this->assertSame('draft', $product->sync_status);
    }

    public function test_recalculate_sets_dirty_when_external_id_present(): void
    {
        $c1 = Component::query()->create([
            'name' => 'Oro',
            'code' => 'ORO-R2',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1_000,
            'is_active' => true,
        ]);

        $c2 = Component::query()->create([
            'name' => 'Plata',
            'code' => 'PLA-R2',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'g',
            'currency' => 'UYU',
            'current_unit_price_minor' => 500,
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Anillo',
            'code' => 'PR-R2',
            'description' => null,
            'category_name' => 'Cat',
            'currency' => 'UYU',
            'cost_minor' => 0,
            'margin_percent' => 0,
            'price_minor' => 0,
            'ind_fact_id' => 3,
            'article_type_id' => 1,
            'external_id' => 42,
            'sync_status' => 'synced',
            'last_synced_at' => null,
        ]);

        $product->components()->attach([
            $c1->id => ['quantity' => 1, 'sort_order' => 0],
            $c2->id => ['quantity' => 1, 'sort_order' => 1],
        ]);

        $product->load('components');
        $product->sync_status = null;

        $product->recalculatePrice();

        $product->refresh();

        $this->assertSame('dirty', $product->sync_status);
    }
}
