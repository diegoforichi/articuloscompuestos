<?php

namespace Tests\Feature;

use App\Filament\Resources\Components\ComponentResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\SyncLogs\SyncLogResource;
use App\Models\Component;
use App\Models\Product;
use App\Models\SyncLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationResourceQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_resources_only_return_current_tenant_records(): void
    {
        $defaultTenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $otherTenant = Tenant::query()->create([
            'name' => 'Otro tenant',
            'slug' => 'otro-tenant',
            'is_active' => true,
            'recalculation_mode' => 'manual',
        ]);

        $user = User::factory()->create([
            'tenant_id' => $defaultTenant->id,
            'is_super_admin' => false,
        ]);
        $this->actingAs($user);

        $defaultComponent = Component::query()->create([
            'tenant_id' => $defaultTenant->id,
            'name' => 'Comp A',
            'code' => 'CMP-A',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);
        Component::query()->create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Comp B',
            'code' => 'CMP-B',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);

        $defaultProduct = Product::query()->create([
            'tenant_id' => $defaultTenant->id,
            'name' => 'Prod A',
            'code' => 'PRD-A',
            'category_name' => 'Cat',
            'currency' => 'UYU',
            'cost_minor' => 1000,
            'margin_percent' => 20,
            'price_minor' => 1200,
            'sync_status' => 'draft',
        ]);
        Product::query()->create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Prod B',
            'code' => 'PRD-B',
            'category_name' => 'Cat',
            'currency' => 'UYU',
            'cost_minor' => 1000,
            'margin_percent' => 20,
            'price_minor' => 1200,
            'sync_status' => 'draft',
        ]);

        $defaultLog = SyncLog::query()->create([
            'tenant_id' => $defaultTenant->id,
            'product_id' => $defaultProduct->id,
            'action' => 'addArticle',
            'success' => true,
        ]);
        SyncLog::query()->create([
            'tenant_id' => $otherTenant->id,
            'product_id' => null,
            'action' => 'addArticle',
            'success' => true,
        ]);

        $this->assertSame([$defaultComponent->id], ComponentResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$defaultProduct->id], ProductResource::getEloquentQuery()->pluck('id')->all());
        $this->assertSame([$defaultLog->id], SyncLogResource::getEloquentQuery()->pluck('id')->all());
    }
}
