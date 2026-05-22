<?php

namespace App\Filament\Resources\IntegrationSettings;

use App\Filament\Resources\IntegrationSettings\Pages\CreateIntegrationSetting;
use App\Filament\Resources\IntegrationSettings\Pages\EditIntegrationSetting;
use App\Filament\Resources\IntegrationSettings\Pages\ListIntegrationSettings;
use App\Filament\Resources\IntegrationSettings\Schemas\IntegrationSettingForm;
use App\Filament\Resources\IntegrationSettings\Tables\IntegrationSettingsTable;
use App\Models\IntegrationSetting;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class IntegrationSettingResource extends Resource
{
    protected static ?string $model = IntegrationSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $modelLabel = 'Integración';

    protected static ?string $pluralModelLabel = 'Integraciones';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return IntegrationSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IntegrationSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIntegrationSettings::route('/'),
            'create' => CreateIntegrationSetting::route('/create'),
            'edit' => EditIntegrationSetting::route('/{record}/edit'),
        ];
    }
}
