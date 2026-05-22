<?php

namespace App\Filament\Resources\Components\Pages;

use App\Filament\Resources\Components\ComponentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComponent extends EditRecord
{
    protected static string $resource = ComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newPriceMinor = (int) ($data['current_unit_price_minor'] ?? 0);
        $oldPriceMinor = (int) $this->record->current_unit_price_minor;

        if ($newPriceMinor !== $oldPriceMinor) {
            $this->record->priceHistories()->create([
                'tenant_id' => $this->record->tenant_id,
                'unit_price_minor' => $oldPriceMinor,
                'currency' => $this->record->currency,
                'effective_at' => now(),
                'notes' => 'Actualización desde panel',
            ]);
        }

        return $data;
    }
}
