<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\IntegrationSetting;
use App\Support\ProductCompositeRules;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        ProductCompositeRules::assertValidComposite(
            $data,
            ($this->data ?? [])['productComponents'] ?? null,
        );

        $settings = IntegrationSetting::active();

        $data['category_name'] = $settings?->default_category_name ?? 'Sin categoría';
        $data['margin_percent'] = $data['margin_percent'] ?? $settings?->default_margin_percent ?? 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->load('components');
        $this->record->recalculatePrice();

        Notification::make()
            ->title('Producto creado')
            ->body('Costo y precio de venta calculados según componentes y utilidad. Revisá y enviá a e-factura cuando corresponda.')
            ->info()
            ->send();
    }
}
