<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\TenantResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    public function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema, 'create');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $adminName = trim((string) ($this->data['admin_name'] ?? ''));
        $adminEmail = trim((string) ($this->data['admin_email'] ?? ''));
        $adminPassword = (string) ($this->data['admin_password'] ?? '');

        $hasAnyAdminField = $adminName !== '' || $adminEmail !== '' || $adminPassword !== '';
        $hasAllAdminFields = $adminName !== '' && $adminEmail !== '' && $adminPassword !== '';

        if ($hasAnyAdminField && ! $hasAllAdminFields) {
            $message = __('messages.tenant.onboarding_incomplete');

            throw ValidationException::withMessages([
                'admin_name' => $message,
                'admin_email' => $message,
                'admin_password' => $message,
            ]);
        }

        if ($hasAllAdminFields && User::query()->where('email', $adminEmail)->exists()) {
            throw ValidationException::withMessages([
                'admin_email' => __('messages.tenant.onboarding_email_exists'),
            ]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $adminName = trim((string) ($this->data['admin_name'] ?? ''));
        $adminEmail = trim((string) ($this->data['admin_email'] ?? ''));
        $adminPassword = (string) ($this->data['admin_password'] ?? '');

        if ($adminName === '' || $adminEmail === '' || $adminPassword === '') {
            return;
        }

        User::query()->create([
            'tenant_id' => $this->record->id,
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => $adminPassword,
            'is_super_admin' => false,
        ]);

        Notification::make()
            ->title(__('messages.tenant.onboarding_success_title'))
            ->body(__('messages.tenant.onboarding_success_body'))
            ->success()
            ->send();
    }
}
