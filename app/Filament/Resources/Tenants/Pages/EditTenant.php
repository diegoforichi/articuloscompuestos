<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\TenantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    public function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema, 'edit');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
