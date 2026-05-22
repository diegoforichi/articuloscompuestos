<?php

namespace App\Jobs;

use App\Models\Component;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecalculateProductsForComponentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $maxExceptions = 2;

    public function __construct(public int $componentId) {}

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function handle(): void
    {
        $component = Component::query()->find($this->componentId);

        if (! $component) {
            return;
        }

        $productIds = $component->products()
            ->select('products.id')
            ->pluck('products.id');

        Log::info('recalculate-products.start', [
            'tenant_id' => $component->tenant_id,
            'component_id' => $component->id,
            'products_total' => $productIds->count(),
        ]);

        $processed = 0;

        Product::query()
            ->whereIn('id', $productIds)
            ->with('components')
            ->chunkById(100, function ($products) use (&$processed): void {
                foreach ($products as $product) {
                    $product->recalculatePrice();
                    $processed++;
                }
            });

        Log::info('recalculate-products.finish', [
            'tenant_id' => $component->tenant_id,
            'component_id' => $component->id,
            'products_processed' => $processed,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $component = Component::query()->find($this->componentId);

        Log::error('recalculate-products.failed', [
            'tenant_id' => $component?->tenant_id,
            'component_id' => $this->componentId,
            'error' => $exception->getMessage(),
        ]);
    }

    public function tags(): array
    {
        $component = Component::query()->find($this->componentId);

        return [
            'recalculate-products',
            'component:'.$this->componentId,
            'tenant:'.($component?->tenant_id ?? 'unknown'),
        ];
    }
}
