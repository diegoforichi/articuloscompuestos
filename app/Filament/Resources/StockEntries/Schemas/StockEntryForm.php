<?php

namespace App\Filament\Resources\StockEntries\Schemas;

use App\Models\Component;
use App\Models\Tenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return $schema
            ->components([
                Section::make('Cabecera')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference')
                            ->label('Referencia')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('FAC-001'),
                        DatePicker::make('entry_date')
                            ->label('Fecha')
                            ->required()
                            ->default(now()),
                        Textarea::make('notes')
                            ->label('Notas')
                            ->columnSpanFull(),
                    ]),
                Section::make('Detalle')
                    ->schema([
                        Repeater::make('items')
                            ->label('Items')
                            ->required()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->columns(3)
                            ->schema([
                                Select::make('component_id')
                                    ->label('Componente')
                                    ->required()
                                    ->searchable()
                                    ->options(
                                        Component::query()
                                            ->where('is_active', true)
                                            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                    ),
                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.0001)
                                    ->step('0.0001'),
                                TextInput::make('unit_cost')
                                    ->label('Costo unitario')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step('0.01')
                                    ->prefix('$'),
                            ]),
                    ]),
            ]);
    }
}
