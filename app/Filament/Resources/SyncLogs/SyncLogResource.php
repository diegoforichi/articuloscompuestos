<?php

namespace App\Filament\Resources\SyncLogs;

use App\Filament\Resources\SyncLogs\Pages\ListSyncLogs;
use App\Filament\Resources\SyncLogs\Tables\SyncLogsTable;
use App\Models\SyncLog;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SyncLogResource extends Resource
{
    protected static ?string $model = SyncLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Log de sincronización';

    protected static ?string $pluralModelLabel = 'Logs de sincronización';

    protected static ?int $navigationSort = 11;

    public static function table(Table $table): Table
    {
        return SyncLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSyncLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = Tenant::resolveCurrentTenantId();

        return parent::getEloquentQuery()
            ->when($tenantId !== null, fn (Builder $query) => $query->where('tenant_id', $tenantId));
    }
}
