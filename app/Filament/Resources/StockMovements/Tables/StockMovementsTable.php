<?php

namespace App\Filament\Resources\StockMovements\Tables;

use App\Filament\Exports\StockMovementExporter;
use App\Models\StockMovement;
use App\Support\StockMovementReferenceFormatter;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('moved_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('component.name')
                    ->label('Componente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('movement_type')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('unit_cost_minor')
                    ->label('Costo')
                    ->formatStateUsing(fn (?int $state): string => $state === null ? '—' : number_format($state / 100, 2)),
                TextColumn::make('balance_after')
                    ->label('Saldo')
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('reference_label')
                    ->label(__('messages.stock_movement.reference_column'))
                    ->state(fn (StockMovement $record): string => StockMovementReferenceFormatter::label($record)),
                TextColumn::make('reference_type')
                    ->label(__('messages.stock_movement.technical_reference'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference_id')
                    ->label('ID ref')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('movement_type')
                    ->label('Tipo')
                    ->options([
                        'IN' => 'IN',
                        'OUT' => 'OUT',
                        'ADJUST' => 'ADJUST',
                    ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(StockMovementExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->columnMapping(false)
                    ->fileName(fn (): string => 'movimientos-stock-'.now()->format('Y-m-d-His')),
            ])
            ->defaultSort('moved_at', 'desc');
    }
}
