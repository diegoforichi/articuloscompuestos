<?php

namespace App\Support;

use App\Models\Component;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ProductCompositeRules
{
    /**
     * @param  array<string, mixed>  $data  Datos del formulario ya deshidratados (campos del modelo).
     * @param  array<int|string, mixed>|null  $rawProductComponents  Estado crudo del repeater; con Repeater::relationship() Filament no incluye esas filas en $data al deshidratar.
     */
    public static function assertValidComposite(array $data, ?array $rawProductComponents = null): void
    {
        $rows = $rawProductComponents !== null
            ? array_values($rawProductComponents)
            : ($data['productComponents'] ?? []);

        if (! is_array($rows)) {
            $rows = [];
        }

        $filledRows = collect($rows)->filter(fn (mixed $row): bool => is_array($row) && filled(Arr::get($row, 'component_id')));

        if ($filledRows->count() < 2) {
            throw ValidationException::withMessages([
                'data.productComponents' => 'Debe agregar al menos 2 componentes para crear un producto compuesto.',
            ]);
        }

        $productCurrency = $data['currency'] ?? null;

        if (! is_string($productCurrency) || $productCurrency === '') {
            throw ValidationException::withMessages([
                'data.currency' => 'El producto y sus componentes deben compartir la misma moneda.',
            ]);
        }

        $componentIds = $filledRows
            ->map(fn (array $row): int => (int) Arr::get($row, 'component_id'))
            ->unique()
            ->values();

        $tenantId = Tenant::resolveCurrentTenantId();

        $components = Component::query()
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->where('is_active', true)
            ->whereIn('id', $componentIds)
            ->get();

        if ($components->count() !== $componentIds->count()) {
            throw ValidationException::withMessages([
                'data.productComponents' => 'Uno o más componentes no existen o están inactivos.',
            ]);
        }

        foreach ($components as $component) {
            if ($component->currency !== $productCurrency) {
                throw ValidationException::withMessages([
                    'data.currency' => 'El producto y sus componentes deben compartir la misma moneda.',
                ]);
            }
        }
    }
}
