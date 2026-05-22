<?php

namespace App\Support;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockMovement;

class StockMovementReferenceFormatter
{
    public static function label(StockMovement $movement): string
    {
        $referenceType = $movement->reference_type;
        $referenceId = $movement->reference_id;

        if ($referenceType === StockEntry::class && $referenceId !== null) {
            return __('messages.stock_movement.reference.stock_entry', ['id' => $referenceId]);
        }

        if ($referenceType === 'consumption' && $referenceId !== null) {
            $productCode = Product::query()->whereKey($referenceId)->value('code');

            return __('messages.stock_movement.reference.consumption_product', [
                'code' => $productCode ?? (string) $referenceId,
            ]);
        }

        if ($referenceType !== null && $referenceId !== null) {
            return $referenceType.' #'.$referenceId;
        }

        return '—';
    }
}
