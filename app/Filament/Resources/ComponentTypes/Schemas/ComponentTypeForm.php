<?php

namespace App\Filament\Resources\ComponentTypes\Schemas;

use App\Models\ComponentType;
use App\Models\Tenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ComponentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Código interno')
                    ->required()
                    ->maxLength(50)
                    ->alphaDash()
                    ->helperText('Solo letras minúsculas, números y guiones. No cambies a la ligera si ya hay componentes usando este tipo.')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('tenant_id', $tenantId),
                    )
                    ->disabled(fn (?ComponentType $record): bool => $record !== null),
                TextInput::make('name')
                    ->label('Nombre visible')
                    ->required()
                    ->maxLength(120)
                    ->helperText('Ej.: Metal, Harina, Mano de obra, Empaque…'),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(65535),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('Los inactivos no aparecen al crear componentes nuevos.'),
            ]);
    }
}
