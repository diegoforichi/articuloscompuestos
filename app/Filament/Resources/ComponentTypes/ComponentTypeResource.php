<?php

namespace App\Filament\Resources\ComponentTypes;

use App\Filament\Resources\ComponentTypes\Pages\CreateComponentType;
use App\Filament\Resources\ComponentTypes\Pages\EditComponentType;
use App\Filament\Resources\ComponentTypes\Pages\ListComponentTypes;
use App\Filament\Resources\ComponentTypes\Schemas\ComponentTypeForm;
use App\Filament\Resources\ComponentTypes\Tables\ComponentTypesTable;
use App\Models\ComponentType;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComponentTypeResource extends Resource
{
    protected static ?string $model = ComponentType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $modelLabel = 'Tipo de componente';

    protected static ?string $pluralModelLabel = 'Tipos de componente';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return ComponentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComponentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComponentTypes::route('/'),
            'create' => CreateComponentType::route('/create'),
            'edit' => EditComponentType::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return parent::getEloquentQuery()
            ->withCount('components')
            ->when($tenantId !== null, fn (Builder $query) => $query->where('tenant_id', $tenantId));
    }
}
