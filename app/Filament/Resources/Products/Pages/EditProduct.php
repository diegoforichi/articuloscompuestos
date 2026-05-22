<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Support\ProductCompositeRules;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        ProductCompositeRules::assertValidComposite(
            $data,
            ($this->data ?? [])['productComponents'] ?? null,
        );

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->load('components');
        $this->record->recalculatePrice();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->record->external_id === null)
                ->modalHeading('Eliminar producto'),
            DeleteAction::make('force_delete')
                ->label('Eliminar')
                ->color('danger')
                ->visible(fn () => $this->record->external_id !== null)
                ->requiresConfirmation()
                ->modalHeading('Eliminar producto sincronizado')
                ->modalDescription('Este producto ya fue enviado a e-factura. Si lo eliminás, seguirá existiendo en el sistema externo pero perderás el vínculo. Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Eliminar de todos modos'),
        ];
    }
}
