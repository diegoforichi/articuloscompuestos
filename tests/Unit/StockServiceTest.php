<?php

namespace Tests\Unit;

use App\Models\Component;
use App\Models\ComponentStock;
use App\Models\Tenant;
use App\Services\StockService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_entry_creates_items_movements_and_updates_snapshot_stock(): void
    {
        $tenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $component = Component::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Harina',
            'code' => 'HAR-01',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);

        $service = new StockService;
        $entry = $service->registerEntry(
            tenantId: $tenant->id,
            userId: null,
            reference: 'FAC-001',
            entryDate: '2026-05-15',
            items: [
                [
                    'component_id' => $component->id,
                    'quantity' => 2.5,
                    'unit_cost_minor' => 500,
                ],
            ],
            notes: 'Ingreso inicial'
        );

        $this->assertCount(1, $entry->items);
        $this->assertDatabaseHas('stock_entries', [
            'id' => $entry->id,
            'tenant_id' => $tenant->id,
            'reference' => 'FAC-001',
        ]);
        $this->assertDatabaseHas('stock_entry_items', [
            'stock_entry_id' => $entry->id,
            'component_id' => $component->id,
            'subtotal_minor' => 1250,
        ]);
        $this->assertDatabaseHas('component_stocks', [
            'tenant_id' => $tenant->id,
            'component_id' => $component->id,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'tenant_id' => $tenant->id,
            'component_id' => $component->id,
            'movement_type' => 'IN',
        ]);

        $stock = ComponentStock::query()->where('component_id', $component->id)->firstOrFail();
        $this->assertSame('2.5000', $stock->quantity_on_hand);
    }

    public function test_consume_component_throws_when_stock_is_insufficient(): void
    {
        $tenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $component = Component::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Levadura',
            'code' => 'LEV-01',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 900,
            'is_active' => true,
        ]);

        ComponentStock::query()->create([
            'tenant_id' => $tenant->id,
            'component_id' => $component->id,
            'quantity_on_hand' => 1.25,
        ]);

        $service = new StockService;

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Stock insuficiente');

        $service->consumeComponent(
            tenantId: $tenant->id,
            componentId: $component->id,
            quantity: 2,
            reason: 'Consumo por receta'
        );

    }

    public function test_register_entry_throws_when_component_does_not_belong_to_tenant(): void
    {
        $defaultTenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $otherTenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'is_active' => true,
            'recalculation_mode' => 'manual',
        ]);

        $foreignComponent = Component::query()->create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Componente externo',
            'code' => 'EXT-01',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);

        $service = new StockService;

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('no pertenece al tenant');

        $service->registerEntry(
            tenantId: $defaultTenant->id,
            userId: null,
            reference: 'FAC-002',
            entryDate: '2026-05-18',
            items: [
                [
                    'component_id' => $foreignComponent->id,
                    'quantity' => 1,
                    'unit_cost_minor' => 500,
                ],
            ],
            notes: null
        );
    }
}
