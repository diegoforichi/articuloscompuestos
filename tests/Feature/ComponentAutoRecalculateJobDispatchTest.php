<?php

namespace Tests\Feature;

use App\Jobs\RecalculateProductsForComponentJob;
use App\Models\Component;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ComponentAutoRecalculateJobDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_price_dispatches_recalculation_job_when_tenant_is_automatic(): void
    {
        Queue::fake();

        $tenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $tenant->update(['recalculation_mode' => 'automatic']);

        $component = Component::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Azucar',
            'code' => 'AZU-01',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 1000,
            'is_active' => true,
        ]);

        $component->updatePrice(1200, 'Ajuste de proveedor');

        Queue::assertPushed(
            RecalculateProductsForComponentJob::class,
            fn (RecalculateProductsForComponentJob $job) => $job->componentId === $component->id
        );
    }

    public function test_update_price_does_not_dispatch_job_when_tenant_is_manual(): void
    {
        Queue::fake();

        $tenant = Tenant::query()->where('slug', 'default')->firstOrFail();
        $tenant->update(['recalculation_mode' => 'manual']);

        $component = Component::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Sal',
            'code' => 'SAL-01',
            'component_type_id' => $this->metalComponentTypeId(),
            'unit' => 'kg',
            'currency' => 'UYU',
            'current_unit_price_minor' => 800,
            'is_active' => true,
        ]);

        $component->updatePrice(900, 'Ajuste');

        Queue::assertNotPushed(RecalculateProductsForComponentJob::class);
    }
}
