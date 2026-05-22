<?php

namespace App\Filament\Resources\SyncLogs\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SyncLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'addArticle' => 'info',
                        'updateArticle' => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make('success')
                    ->label('OK')
                    ->boolean(),
                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(60)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Acción')
                    ->options([
                        'addArticle' => 'Alta',
                        'updateArticle' => 'Actualización',
                    ]),
                SelectFilter::make('success')
                    ->label('Resultado')
                    ->options([
                        '1' => 'Exitoso',
                        '0' => 'Fallido',
                    ]),
            ])
            ->recordActions([
                Action::make('view_detail')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalle del log')
                    ->modalContent(fn ($record) => view('filament.sync-log-detail', ['log' => $record]))
                    ->modalSubmitAction(false),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
