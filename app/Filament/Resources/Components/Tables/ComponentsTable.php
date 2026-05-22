<?php

namespace App\Filament\Resources\Components\Tables;

use App\Models\Component;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('componentType.name')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('unit')
                    ->label('Unidad'),
                TextColumn::make('currency')
                    ->label('Moneda'),
                TextColumn::make('current_unit_price_minor')
                    ->label('Precio')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 100, 2))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('component_type_id')
                    ->label('Tipo')
                    ->relationship(
                        'componentType',
                        'name',
                        fn ($query) => $query->when($tenantId !== null, fn ($innerQuery) => $innerQuery->where('tenant_id', $tenantId))
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('currency')
                    ->label('Moneda')
                    ->options([
                        'UYU' => 'UYU',
                        'USD' => 'USD',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('recalculate_products')
                    ->label('Recalcular productos')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Recalcular productos afectados')
                    ->modalDescription(fn (Component $record): string => 'Se recalcularán los precios de todos los productos que usan "'.$record->name.'".')
                    ->action(function (Component $record) {
                        $products = $record->products;
                        $count = 0;

                        foreach ($products as $product) {
                            $product->load('components');
                            $product->recalculatePrice();
                            $count++;
                        }

                        Notification::make()
                            ->title('Recálculo completado')
                            ->body("Se actualizaron {$count} producto(s).")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
