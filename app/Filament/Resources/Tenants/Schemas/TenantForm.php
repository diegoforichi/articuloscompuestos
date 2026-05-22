<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema, string $operation = 'edit'): Schema
    {
        $components = [
            Section::make(__('messages.tenant.section'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('attributes.name'))
                        ->required()
                        ->maxLength(120),
                    TextInput::make('slug')
                        ->label(__('attributes.slug'))
                        ->required()
                        ->maxLength(120)
                        ->alphaDash()
                        ->unique(ignoreRecord: true),
                    Toggle::make('is_active')
                        ->label(__('messages.active'))
                        ->default(true),
                    Select::make('recalculation_mode')
                        ->label(__('messages.tenant.recalculation_mode'))
                        ->required()
                        ->default('manual')
                        ->options([
                            'manual' => __('messages.tenant.recalculation_manual'),
                            'automatic' => __('messages.tenant.recalculation_automatic'),
                        ]),
                ]),
        ];

        if ($operation === 'create') {
            $components[] = Section::make(__('messages.tenant.onboarding_section'))
                ->description(__('messages.tenant.onboarding_description'))
                ->columns(3)
                ->schema([
                    TextInput::make('admin_name')
                        ->label(__('messages.tenant.admin_name'))
                        ->maxLength(255),
                    TextInput::make('admin_email')
                        ->label(__('messages.tenant.admin_email'))
                        ->email()
                        ->maxLength(255),
                    TextInput::make('admin_password')
                        ->label(__('messages.tenant.admin_password'))
                        ->password()
                        ->revealable()
                        ->minLength(8),
                ]);
        }

        return $schema->components($components);
    }
}
