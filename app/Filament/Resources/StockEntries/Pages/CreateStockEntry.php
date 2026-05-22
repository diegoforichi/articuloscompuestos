<?php

namespace App\Filament\Resources\StockEntries\Pages;

use App\Filament\Resources\StockEntries\StockEntryResource;
use App\Models\Tenant;
use App\Models\User;
use App\Services\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateStockEntry extends CreateRecord
{
    protected static string $resource = StockEntryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        if ($tenantId === null) {
            throw ValidationException::withMessages([
                'data.reference' => 'No se pudo resolver tenant actual.',
            ]);
        }

        /** @var User|null $user */
        $user = Auth::user();
        $items = collect($data['items'] ?? [])->map(function (array $row): array {
            return [
                'component_id' => (int) $row['component_id'],
                'quantity' => (float) $row['quantity'],
                'unit_cost_minor' => (int) round(((float) ($row['unit_cost'] ?? 0)) * 100),
            ];
        })->all();

        return app(StockService::class)->registerEntry(
            tenantId: $tenantId,
            userId: $user?->id,
            reference: (string) $data['reference'],
            entryDate: (string) $data['entry_date'],
            items: $items,
            notes: $data['notes'] ?? null,
        );
    }
}
