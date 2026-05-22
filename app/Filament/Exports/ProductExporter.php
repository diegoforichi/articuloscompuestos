<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')
                ->label('Código'),
            ExportColumn::make('name')
                ->label('Nombre'),
            ExportColumn::make('category_name')
                ->label('Categoría'),
            ExportColumn::make('currency')
                ->label('Moneda'),
            ExportColumn::make('cost_minor')
                ->label('Costo')
                ->formatStateUsing(fn (int $state): string => number_format($state / 100, 2, '.', '')),
            ExportColumn::make('margin_percent')
                ->label('Utilidad %'),
            ExportColumn::make('price_minor')
                ->label('Precio venta')
                ->formatStateUsing(fn (int $state): string => number_format($state / 100, 2, '.', '')),
            ExportColumn::make('sync_status')
                ->label('Estado sync'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = number_format($export->successful_rows);

        return "Se exportaron {$count} productos.";
    }
}
