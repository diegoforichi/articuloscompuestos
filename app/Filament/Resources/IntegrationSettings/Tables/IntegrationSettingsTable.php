<?php

namespace App\Filament\Resources\IntegrationSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IntegrationSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('environment_name')
                    ->label('Ambiente')
                    ->sortable(),
                TextColumn::make('base_url')
                    ->label('URL base')
                    ->limit(40),
                TextColumn::make('rut_emisor')
                    ->label('RUT Emisor'),
                TextColumn::make('default_category_name')
                    ->label('Categoría')
                    ->placeholder('—'),
                TextColumn::make('default_prefix')
                    ->label('Prefijo')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
