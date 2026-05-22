<?php

namespace App\Filament\Resources\Tenants\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recalculation_mode')
                    ->label('Recalculo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'automatic' ? 'Automático' : 'Manual'),
                TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->badge()
                    ->color(fn (int $state): string => $state === 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn (int $state): string => $state === 0
                        ? __('messages.tenant.no_users_badge')
                        : (string) $state)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => ($record->users_count ?? 0) === 0),
            ])
            ->defaultSort('name');
    }
}
