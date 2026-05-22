<?php

namespace App\Filament\Exports;

use App\Models\StockMovement;
use App\Support\StockMovementReferenceFormatter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class StockMovementExporter extends Exporter
{
    protected static ?string $model = StockMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('moved_at')
                ->label('Fecha'),
            ExportColumn::make('component.name')
                ->label('Componente'),
            ExportColumn::make('movement_type')
                ->label('Tipo'),
            ExportColumn::make('quantity')
                ->label('Cantidad'),
            ExportColumn::make('unit_cost_minor')
                ->label('Costo')
                ->formatStateUsing(fn (?int $state): string => $state === null ? '' : number_format($state / 100, 2, '.', '')),
            ExportColumn::make('balance_after')
                ->label('Saldo'),
            ExportColumn::make('reference_label')
                ->label('Referencia')
                ->state(fn (StockMovement $record): string => StockMovementReferenceFormatter::label($record)),
            ExportColumn::make('notes')
                ->label('Notas'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = number_format($export->successful_rows);

        return "Se exportaron {$count} movimientos de stock.";
    }

    /**
     * @param  Builder<StockMovement>  $query
     * @return Builder<StockMovement>
     */
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with('component');
    }
}
