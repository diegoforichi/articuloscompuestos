<?php

namespace Tests\Feature;

use App\Models\IntegrationSetting;
use App\Models\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationSettingUniquePerTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_disallows_more_than_one_integration_per_tenant(): void
    {
        $tenant = Tenant::query()->where('slug', Tenant::DEFAULT_SLUG)->firstOrFail();

        IntegrationSetting::query()->create([
            'tenant_id' => $tenant->id,
            'environment_name' => 'Demo',
            'base_url' => 'https://example.test',
            'token' => 'token-1',
            'rut_emisor' => '000000000016',
            'default_category_name' => 'Confeccionados',
            'default_prefix' => 'DEM-',
            'default_margin_percent' => 20,
            'remote_filter_mode' => 'category',
            'is_active' => true,
        ]);

        $this->expectException(QueryException::class);

        IntegrationSetting::query()->create([
            'tenant_id' => $tenant->id,
            'environment_name' => 'Producción',
            'base_url' => 'https://prod.example.test',
            'token' => 'token-2',
            'rut_emisor' => '000000000016',
            'default_category_name' => 'Confeccionados',
            'default_prefix' => 'PRD-',
            'default_margin_percent' => 25,
            'remote_filter_mode' => 'category',
            'is_active' => false,
        ]);
    }
}
