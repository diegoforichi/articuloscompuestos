<?php

namespace App\Filament\Resources\StockEntries\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entry_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('entry_total')
                    ->label('Total')
                    ->state(function ($record): string {
                        $total = (int) $record->items->sum('subtotal_minor');

                        return number_format($total / 100, 2);
                    }),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('entry_date', 'desc');
    }
}
