<?php

namespace App\Services;

use App\Models\Component;
use App\Models\ComponentStock;
use App\Models\StockEntry;
use App\Models\StockMovement;
use DomainException;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * @param  array<int, array{component_id:int, quantity:float|int|string, unit_cost_minor:int|float|string}>  $items
     */
    public function registerEntry(
        int $tenantId,
        ?int $userId,
        string $reference,
        string $entryDate,
        array $items,
        ?string $notes = null,
    ): StockEntry {
        return DB::transaction(function () use ($tenantId, $userId, $reference, $entryDate, $items, $notes) {
            $entry = StockEntry::query()->create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'reference' => $reference,
                'entry_date' => $entryDate,
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $componentId = (int) $item['component_id'];
                $quantity = round((float) $item['quantity'], 4);
                $unitCostMinor = (int) round((float) $item['unit_cost_minor']);
                $subtotalMinor = (int) round($quantity * $unitCostMinor);

                $componentExists = Component::query()
                    ->whereKey($componentId)
                    ->where('tenant_id', $tenantId)
                    ->exists();

                if (! $componentExists) {
                    throw new DomainException('El componente no pertenece al tenant actual.');
                }

                $entry->items()->create([
                    'component_id' => $componentId,
                    'quantity' => $quantity,
                    'unit_cost_minor' => $unitCostMinor,
                    'subtotal_minor' => $subtotalMinor,
                ]);

                $stock = ComponentStock::query()
                    ->firstOrCreate(
                        ['tenant_id' => $tenantId, 'component_id' => $componentId],
                        ['quantity_on_hand' => 0],
                    );

                $balanceAfter = round(((float) $stock->quantity_on_hand) + $quantity, 4);
                $stock->update(['quantity_on_hand' => $balanceAfter]);

                StockMovement::query()->create([
                    'tenant_id' => $tenantId,
                    'component_id' => $componentId,
                    'movement_type' => 'IN',
                    'quantity' => $quantity,
                    'unit_cost_minor' => $unitCostMinor,
                    'balance_after' => $balanceAfter,
                    'reference_type' => StockEntry::class,
                    'reference_id' => $entry->id,
                    'notes' => $notes,
                    'moved_at' => now(),
                ]);
            }

            return $entry->load('items');
        });
    }

    public function consumeComponent(
        int $tenantId,
        int $componentId,
        float $quantity,
        string $reason,
        ?int $referenceId = null,
    ): void {
        DB::transaction(function () use ($tenantId, $componentId, $quantity, $reason, $referenceId) {
            $componentExists = Component::query()
                ->whereKey($componentId)
                ->where('tenant_id', $tenantId)
                ->exists();

            if (! $componentExists) {
                throw new DomainException('El componente no pertenece al tenant actual.');
            }

            $stock = ComponentStock::query()
                ->firstOrCreate(
                    ['tenant_id' => $tenantId, 'component_id' => $componentId],
                    ['quantity_on_hand' => 0],
                );

            $current = (float) $stock->quantity_on_hand;
            $newBalance = round($current - $quantity, 4);

            if ($newBalance < 0) {
                throw new DomainException('Stock insuficiente para el componente.');
            }

            $stock->update(['quantity_on_hand' => $newBalance]);

            StockMovement::query()->create([
                'tenant_id' => $tenantId,
                'component_id' => $componentId,
                'movement_type' => 'OUT',
                'quantity' => $quantity,
                'unit_cost_minor' => null,
                'balance_after' => $newBalance,
                'reference_type' => 'consumption',
                'reference_id' => $referenceId,
                'notes' => $reason,
                'moved_at' => now(),
            ]);
        });
    }
}
