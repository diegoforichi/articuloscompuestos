<?php

namespace App\Filament\Resources\ComponentStocks\Tables;

use App\Filament\Exports\ComponentStockExporter;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComponentStocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('component.code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('component.name')
                    ->label('Componente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('component.unit')
                    ->label('Unidad'),
                TextColumn::make('quantity_on_hand')
                    ->label('Stock actual')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ComponentStockExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->columnMapping(false)
                    ->fileName(fn (): string => 'saldos-componentes-'.now()->format('Y-m-d-His')),
            ])
            ->defaultSort('component.name');
    }
}
