<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'category_name',
        'currency',
        'cost_minor',
        'margin_percent',
        'price_minor',
        'ind_fact_id',
        'article_type_id',
        'external_id',
        'sync_status',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'cost_minor' => 'integer',
            'margin_percent' => 'decimal:2',
            'price_minor' => 'integer',
            'ind_fact_id' => 'integer',
            'article_type_id' => 'integer',
            'external_id' => 'integer',
            'last_synced_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if ($product->tenant_id === null) {
                $product->tenant_id = Tenant::resolveCurrentTenantId();
            }
        });
    }

    public function setMarginPercentAttribute(mixed $value): void
    {
        $numeric = is_numeric($value) ? (float) $value : 0.0;

        $this->attributes['margin_percent'] = (string) max(0.0, min(100.0, $numeric));
    }

    public function productComponents(): HasMany
    {
        return $this->hasMany(ProductComponent::class);
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'product_components')
            ->withPivot('quantity', 'sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class);
    }

    /**
     * Genera el próximo código autoincremental con el prefijo configurado.
     */
    public static function generateNextCode(): string
    {
        $tenantId = Tenant::resolveCurrentTenantId();
        $settings = IntegrationSetting::active($tenantId);
        $prefix = $settings?->default_prefix ?? '';

        $lastProduct = static::query()
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        if ($lastProduct) {
            $lastNumber = (int) str_replace($prefix, '', $lastProduct->code);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedPriceAttribute(): float
    {
        return round($this->price_minor / 100, 2);
    }

    /**
     * Recalcula costo (suma de componentes) y precio de venta (costo + utilidad %).
     */
    public function recalculatePrice(): int
    {
        $margin = (float) $this->margin_percent;

        $costMinor = 0;
        $breakdown = [];

        foreach ($this->components as $component) {
            $qty = (float) $component->pivot->quantity;
            $lineTotal = (int) round($component->current_unit_price_minor * $qty);
            $costMinor += $lineTotal;

            $breakdown[] = [
                'component_id' => $component->id,
                'code' => $component->code,
                'name' => $component->name,
                'unit_price_minor' => $component->current_unit_price_minor,
                'quantity' => $qty,
                'line_total_minor' => $lineTotal,
            ];
        }

        $salePriceMinor = $this->salePriceFromCostAndMargin($costMinor, $margin);

        if ($costMinor === (int) $this->cost_minor && $salePriceMinor === (int) $this->price_minor) {
            return $salePriceMinor;
        }

        $this->update([
            'cost_minor' => $costMinor,
            'price_minor' => $salePriceMinor,
            'sync_status' => $this->external_id ? 'dirty' : ($this->sync_status ?? 'draft'),
        ]);

        $this->priceHistories()->create([
            'tenant_id' => $this->tenant_id,
            'cost_minor' => $costMinor,
            'margin_percent' => $margin,
            'price_minor' => $salePriceMinor,
            'currency' => $this->currency,
            'calculated_at' => now(),
            'breakdown_snapshot' => $breakdown,
        ]);

        return $salePriceMinor;
    }

    /**
     * Precio de venta en unidades menores a partir del costo y el margen (0–100).
     */
    public function salePriceFromCostAndMargin(int $costMinor, float $marginPercent): int
    {
        $margin = max(0.0, min(100.0, $marginPercent));

        return (int) round($costMinor * (100 + $margin) / 100);
    }

    public function isSyncStatusSynced(): bool
    {
        return $this->sync_status === 'synced';
    }

    public function isSyncStatusDirty(): bool
    {
        return $this->sync_status === 'dirty';
    }

    public function isSyncStatusDraft(): bool
    {
        return $this->sync_status === 'draft';
    }

    public function hasExternalId(): bool
    {
        return $this->external_id !== null;
    }

    /**
     * @return int Mapeo de moneda para la API externa.
     */
    public function getMonIdAttribute(): int
    {
        return match ($this->currency) {
            'USD' => 3,
            default => 1,
        };
    }
}
