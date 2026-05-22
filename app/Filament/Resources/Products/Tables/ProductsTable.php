<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Exports\ProductExporter;
use App\Models\Product;
use App\Services\EFacturaService;
use App\Services\StockService;
use DomainException;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('category_name')
                    ->label('Categoría')
                    ->sortable(),
                TextColumn::make('currency')
                    ->label('Moneda'),
                TextColumn::make('cost_minor')
                    ->label('Costo')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 100, 2))
                    ->sortable(),
                TextColumn::make('margin_percent')
                    ->label('Utilidad %')
                    ->formatStateUsing(fn ($state): string => $state === null ? '—' : number_format((float) $state, 2))
                    ->sortable(),
                TextColumn::make('price_minor')
                    ->label('Precio venta')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 100, 2))
                    ->sortable(),
                TextColumn::make('sync_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'synced' => 'success',
                        'dirty' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('external_id')
                    ->label('ID externo')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_synced_at')
                    ->label('Último sync')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Nunca')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sync_status')
                    ->label('Estado sync')
                    ->options([
                        'draft' => 'Borrador',
                        'synced' => 'Sincronizado',
                        'dirty' => 'Desactualizado',
                        'error' => 'Error',
                    ]),
                SelectFilter::make('currency')
                    ->label('Moneda')
                    ->options([
                        'UYU' => 'UYU',
                        'USD' => 'USD',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('recalculate')
                    ->label('Recalcular')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Recalcular precio')
                    ->modalDescription('Se recalcularán el costo (componentes) y el precio de venta según la utilidad del producto.')
                    ->action(function (Product $record) {
                        $record->load('components');
                        $newPrice = $record->recalculatePrice();
                        $record->refresh();

                        Notification::make()
                            ->title('Precio recalculado')
                            ->body(
                                'Costo: $'.number_format($record->cost_minor / 100, 2)
                                .' · Precio venta: $'.number_format($newPrice / 100, 2)
                            )
                            ->success()
                            ->send();
                    }),
                Action::make('sync')
                    ->label('Enviar')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar a e-factura')
                    ->modalDescription(fn (Product $record): string => $record->hasExternalId()
                        ? 'Se actualizarán precio, nombre, descripción y moneda en e-factura.'
                        : 'Se creará el producto en el sistema de facturación.'
                    )
                    ->action(function (Product $record) {
                        self::syncProduct($record);
                    }),
                Action::make('consume_stock')
                    ->label('Consumir stock')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('messages.products.consume_modal_heading'))
                    ->modalDescription(__('messages.products.consume_modal_description'))
                    ->form([
                        TextInput::make('units')
                            ->label('Unidades a consumir')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0.0001)
                            ->step('0.0001')
                            ->live()
                            ->helperText(function (Product $record, Get $get): string {
                                $units = (float) ($get('units') ?? 1);
                                $record->loadMissing('components');

                                if ($record->components->isEmpty()) {
                                    return __('messages.products.consume_preview_empty');
                                }

                                $lines = $record->components->map(function ($component) use ($units): string {
                                    $requiredQuantity = round(((float) $component->pivot->quantity) * $units, 4);

                                    return __('messages.products.consume_preview_line', [
                                        'name' => $component->name,
                                        'quantity' => $requiredQuantity,
                                        'unit' => $component->unit,
                                    ]);
                                });

                                return $lines->implode(' · ');
                            }),
                    ])
                    ->action(function (Product $record, array $data) {
                        $record->load('components');
                        $units = (float) ($data['units'] ?? 1);
                        $service = app(StockService::class);

                        try {
                            foreach ($record->components as $component) {
                                $requiredQuantity = round(((float) $component->pivot->quantity) * $units, 4);

                                $service->consumeComponent(
                                    tenantId: (int) $record->tenant_id,
                                    componentId: (int) $component->id,
                                    quantity: $requiredQuantity,
                                    reason: 'Consumo por producto '.$record->code,
                                    referenceId: (int) $record->id,
                                );
                            }
                        } catch (DomainException $exception) {
                            Notification::make()
                                ->title('Consumo rechazado')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Stock consumido')
                            ->body('Se registró el consumo para '.$units.' unidad(es) del producto.')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ProductExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->columnMapping(false)
                    ->fileName(fn (): string => 'productos-'.now()->format('Y-m-d-His')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_recalculate')
                        ->label('Recalcular seleccionados')
                        ->icon('heroicon-o-calculator')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Recalcular productos')
                        ->modalDescription('Se recalcularán los precios de todos los productos seleccionados.')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->load('components');
                                $record->recalculatePrice();
                                $count++;
                            }

                            Notification::make()
                                ->title('Recálculo completado')
                                ->body("Se recalcularon {$count} productos.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_sync')
                        ->label('Enviar seleccionados a e-factura')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Enviar a e-factura')
                        ->modalDescription('Se enviarán o actualizarán los productos seleccionados en el sistema de facturación.')
                        ->action(function (Collection $records) {
                            $ok = 0;
                            $fail = 0;

                            foreach ($records as $record) {
                                $result = self::syncProduct($record, silent: true);
                                $result ? $ok++ : $fail++;
                            }

                            $msg = "Exitosos: {$ok}";
                            if ($fail > 0) {
                                $msg .= " | Fallidos: {$fail}";
                            }

                            Notification::make()
                                ->title('Sincronización completada')
                                ->body($msg)
                                ->color($fail > 0 ? 'warning' : 'success')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    private static function syncProduct(Product $record, bool $silent = false): bool
    {
        $service = EFacturaService::make();

        if (! $service) {
            if (! $silent) {
                Notification::make()
                    ->title('Sin configuración')
                    ->body('No hay una configuración de integración activa.')
                    ->danger()
                    ->send();
            }

            return false;
        }

        $isNew = ! $record->hasExternalId();

        $result = $isNew
            ? $service->addArticle($record)
            : $service->updateArticle($record);

        if (! $silent) {
            if ($result['success']) {
                Notification::make()
                    ->title('Sincronización exitosa')
                    ->body($isNew
                        ? 'Producto creado en e-factura con ID: '.$result['entity']
                        : 'Producto actualizado en e-factura.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error de sincronización')
                    ->body($result['error'] ?? 'Error desconocido')
                    ->danger()
                    ->send();
            }
        }

        return $result['success'];
    }
}
