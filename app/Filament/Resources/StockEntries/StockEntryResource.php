<?php

namespace App\Filament\Resources\StockEntries;

use App\Filament\Resources\StockEntries\Pages\CreateStockEntry;
use App\Filament\Resources\StockEntries\Pages\ListStockEntries;
use App\Filament\Resources\StockEntries\Schemas\StockEntryForm;
use App\Filament\Resources\StockEntries\Tables\StockEntriesTable;
use App\Models\StockEntry;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockEntryResource extends Resource
{
    protected static ?string $model = StockEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static ?string $modelLabel = 'Entrada de stock';

    protected static ?string $pluralModelLabel = 'Entradas de stock';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return StockEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockEntries::route('/'),
            'create' => CreateStockEntry::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return parent::getEloquentQuery()
            ->with(['items.component', 'user'])
            ->when($tenantId !== null, fn (Builder $query) => $query->where('tenant_id', $tenantId));
    }
}
