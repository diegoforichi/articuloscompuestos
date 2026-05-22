<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Support\StockMovementReferenceFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementReferenceFormatterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_formats_stock_entry_reference(): void
    {
        $movement = new StockMovement([
            'reference_type' => StockEntry::class,
            'reference_id' => 42,
        ]);

        $this->assertSame('Entrada #42', StockMovementReferenceFormatter::label($movement));
    }

    public function test_it_formats_consumption_reference_with_product_code(): void
    {
        $tenant = Tenant::query()->where('slug', Tenant::DEFAULT_SLUG)->firstOrFail();

        $product = Product::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Producto test',
            'code' => 'PROD-TEST',
            'description' => null,
            'category_name' => 'Test',
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

        $movement = new StockMovement([
            'reference_type' => 'consumption',
            'reference_id' => $product->id,
        ]);

        $this->assertSame(
            'Consumo producto PROD-TEST',
            StockMovementReferenceFormatter::label($movement),
        );
    }
}
