<?php

namespace App\Filament\Exports;

use App\Models\ComponentStock;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ComponentStockExporter extends Exporter
{
    protected static ?string $model = ComponentStock::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('component.code')
                ->label('Código'),
            ExportColumn::make('component.name')
                ->label('Componente'),
            ExportColumn::make('component.unit')
                ->label('Unidad'),
            ExportColumn::make('quantity_on_hand')
                ->label('Stock actual'),
            ExportColumn::make('updated_at')
                ->label('Actualizado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = number_format($export->successful_rows);

        return "Se exportaron {$count} saldos de componentes.";
    }

    /**
     * @param  Builder<ComponentStock>  $query
     * @return Builder<ComponentStock>
     */
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with('component');
    }
}
