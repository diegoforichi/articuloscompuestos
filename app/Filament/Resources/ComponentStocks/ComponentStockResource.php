<?php

namespace App\Filament\Resources\ComponentStocks;

use App\Filament\Resources\ComponentStocks\Pages\ListComponentStocks;
use App\Filament\Resources\ComponentStocks\Tables\ComponentStocksTable;
use App\Models\ComponentStock;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComponentStockResource extends Resource
{
    protected static ?string $model = ComponentStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $modelLabel = 'Saldo de componente';

    protected static ?string $pluralModelLabel = 'Saldos de componentes';

    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return ComponentStocksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComponentStocks::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return parent::getEloquentQuery()
            ->with('component')
            ->when($tenantId !== null, fn (Builder $query) => $query->where('tenant_id', $tenantId));
    }
}
