<?php

namespace App\Filament\Resources\IntegrationSettings\Schemas;

use App\Models\IntegrationSetting;
use App\Models\Tenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class IntegrationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Conexión')
                    ->columns(2)
                    ->schema([
                        Select::make('tenant_id')
                            ->label('Tenant')
                            ->required()
                            ->searchable()
                            ->helperText(__('messages.integration.tenant_select_helper'))
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule): Unique => $rule,
                            )
                            ->validationMessages([
                                'unique' => __('messages.integration.tenant_already_configured'),
                            ])
                            ->options(function (?IntegrationSetting $record) {
                                $usedTenantIds = IntegrationSetting::query()
                                    ->when($record !== null, fn ($query) => $query->where('id', '!=', $record->id))
                                    ->whereNotNull('tenant_id')
                                    ->pluck('tenant_id');

                                return Tenant::query()
                                    ->when($usedTenantIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $usedTenantIds))
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            }),
                        TextInput::make('environment_name')
                            ->label('Nombre del ambiente')
                            ->required()
                            ->placeholder('Demo, Producción...')
                            ->maxLength(50),
                        TextInput::make('base_url')
                            ->label('URL base')
                            ->required()
                            ->url()
                            ->placeholder('https://...'),
                        TextInput::make('token')
                            ->label('Token Bearer')
                            ->required()
                            ->password()
                            ->revealable(),
                        TextInput::make('rut_emisor')
                            ->label('RUT Emisor')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('auth_header_value')
                            ->label('Auth (seguridad adicional)')
                            ->password()
                            ->revealable()
                            ->maxLength(500)
                            ->helperText('Opcional en demo. En producción el proveedor puede exigir el header `Auth` con un valor fijo.'),
                        TextInput::make('origin_url')
                            ->label('Origin (URL origen)')
                            ->url()
                            ->maxLength(512)
                            ->helperText('Opcional en demo. En producción puede exigirse el header `Origin` con la URL permitida (sin barra final).'),
                    ]),
                Section::make('Configuración de productos')
                    ->columns(2)
                    ->schema([
                        TextInput::make('default_category_name')
                            ->label('Categoría por defecto')
                            ->placeholder('Confeccionados')
                            ->maxLength(50),
                        TextInput::make('default_prefix')
                            ->label('Prefijo de código (opcional)')
                            ->placeholder('ZAF-')
                            ->maxLength(20),
                        TextInput::make('default_margin_percent')
                            ->label('Utilidad por defecto (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->default(0)
                            ->suffix('%'),
                        Select::make('remote_filter_mode')
                            ->label('Modo de filtro remoto (futuro)')
                            ->options([
                                'category' => 'Por categoría',
                                'prefix' => 'Por prefijo',
                                'both' => 'Ambos',
                            ])
                            ->default('category'),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->helperText(__('messages.integration.active_toggle_helper')),
                    ]),
            ]);
    }
}
