<?php

namespace App\Filament\Resources\Components\Schemas;

use App\Models\Tenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('tenant_id', $tenantId),
                    )
                    ->maxLength(50),
                Select::make('component_type_id')
                    ->label('Tipo')
                    ->relationship(
                        name: 'componentType',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query
                            ->when($tenantId !== null, fn (Builder $innerQuery): Builder => $innerQuery->where('tenant_id', $tenantId))
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('name'),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Definí tipos en «Tipos de componente» (ej. harina, manteca, metal…).'),
                TextInput::make('unit')
                    ->label('Unidad')
                    ->required()
                    ->placeholder('g, kg, unidad, servicio…')
                    ->maxLength(20),
                Select::make('currency')
                    ->label('Moneda')
                    ->required()
                    ->options([
                        'UYU' => 'UYU - Peso Uruguayo',
                        'USD' => 'USD - Dólar',
                    ])
                    ->default('UYU'),
                TextInput::make('current_unit_price_minor')
                    ->label('Precio unitario')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step('0.01')
                    ->default(0)
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state(
                            $state ? number_format((int) $state / 100, 2, '.', '') : '0.00'
                        );
                    })
                    ->dehydrateStateUsing(fn ($state) => (int) round(((float) ($state ?? 0)) * 100)),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
