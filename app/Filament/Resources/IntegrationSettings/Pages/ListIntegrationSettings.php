<?php

namespace App\Filament\Resources\IntegrationSettings\Pages;

use App\Filament\Resources\IntegrationSettings\IntegrationSettingResource;
use App\Models\IntegrationSetting;
use App\Models\Tenant;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIntegrationSettings extends ListRecords
{
    protected static string $resource = IntegrationSettingResource::class;

    public function getSubheading(): ?string
    {
        if ($this->hasTenantAvailableForIntegration()) {
            return null;
        }

        return __('messages.integration.no_tenant_available_body');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => $this->hasTenantAvailableForIntegration()),
        ];
    }

    protected function hasTenantAvailableForIntegration(): bool
    {
        $tenantsWithIntegration = IntegrationSetting::query()
            ->whereNotNull('tenant_id')
            ->pluck('tenant_id');

        return Tenant::query()
            ->whereNotIn('id', $tenantsWithIntegration)
            ->exists();
    }
}
