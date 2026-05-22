<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Component;
use App\Models\IntegrationSetting;
use App\Models\Product;
use App\Models\Tenant;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return $schema
            ->components([
                Section::make('Datos del producto')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('cost_reference')
                            ->label('Costo')
                            ->visible(fn (?Product $record) => $record !== null)
                            ->content(function (?Product $record): string {
                                if ($record === null) {
                                    return '—';
                                }

                                return '$'.number_format($record->cost_minor / 100, 2).' '.($record->currency ?? '');
                            }),
                        Placeholder::make('sale_price_reference')
                            ->label('Precio venta')
                            ->visible(fn (?Product $record) => $record !== null)
                            ->content(function (?Product $record): string {
                                if ($record === null) {
                                    return '—';
                                }

                                return '$'.number_format($record->price_minor / 100, 2).' '.($record->currency ?? '');
                            }),
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(80),
                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->where('tenant_id', $tenantId),
                            )
                            ->maxLength(50)
                            ->default(fn () => Product::generateNextCode())
                            ->disabled(fn ($record) => $record?->external_id !== null)
                            ->dehydrated()
                            ->helperText(fn ($record) => $record?->external_id
                                ? 'No modificable después de enviar a e-factura.'
                                : null),
                        Placeholder::make('category_display')
                            ->label('Categoría')
                            ->content(function () {
                                $settings = IntegrationSetting::active();

                                return $settings?->default_category_name ?? 'Sin configuración activa';
                            })
                            ->helperText(fn (?Product $record) => $record === null
                                ? 'Se toma de la configuración de integración. Debe existir en el portal de e-factura.'
                                : null),
                        Select::make('currency')
                            ->label('Moneda')
                            ->required()
                            ->options([
                                'UYU' => 'UYU - Peso Uruguayo',
                                'USD' => 'USD - Dólar',
                            ])
                            ->default('UYU')
                            ->helperText('El producto y sus componentes deben compartir la misma moneda.'),
                        TextInput::make('margin_percent')
                            ->label('Utilidad (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->default(fn () => IntegrationSetting::active()?->default_margin_percent ?? 0)
                            ->suffix('%'),
                        Select::make('ind_fact_id')
                            ->label('Indicador facturación')
                            ->required()
                            ->options([
                                1 => 'Exento',
                                2 => 'IVA 10',
                                3 => 'IVA 22',
                            ])
                            ->default(3)
                            ->disabled(fn ($record) => $record?->external_id !== null)
                            ->dehydrated()
                            ->helperText(fn ($record) => $record?->external_id
                                ? 'No modificable después de enviar a e-factura.'
                                : null),
                        Select::make('article_type_id')
                            ->label('Tipo artículo')
                            ->required()
                            ->options([
                                1 => 'Producto',
                                2 => 'Servicio',
                            ])
                            ->default(1)
                            ->disabled(fn ($record) => $record?->external_id !== null)
                            ->dehydrated()
                            ->helperText(fn ($record) => $record?->external_id
                                ? 'No modificable después de enviar a e-factura.'
                                : null),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
                Section::make('Composición')
                    ->schema([
                        Repeater::make('productComponents')
                            ->label('Componentes')
                            ->relationship()
                            ->schema([
                                Select::make('component_id')
                                    ->label('Componente')
                                    ->options(
                                        Component::query()
                                            ->where('is_active', true)
                                            ->when(Tenant::resolveCurrentTenantId() !== null, fn ($query) => $query->where('tenant_id', Tenant::resolveCurrentTenantId()))
                                            ->pluck('name', 'id')
                                    )
                                    ->required()
                                    ->searchable(),
                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.0001)
                                    ->step('0.0001')
                                    ->default(1),
                                TextInput::make('sort_order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar componente')
                            ->reorderable()
                            ->collapsible()
                            ->collapsed(),
                    ]),
            ]);
    }
}
