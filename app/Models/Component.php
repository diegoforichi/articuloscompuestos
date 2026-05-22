<?php

namespace App\Models;

use App\Jobs\RecalculateProductsForComponentJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'component_type_id',
        'unit',
        'currency',
        'current_unit_price_minor',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_unit_price_minor' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Component $component) {
            if ($component->tenant_id === null) {
                $component->tenant_id = Tenant::resolveCurrentTenantId();
            }
        });
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(ComponentPriceHistory::class);
    }

    /**
     * @return BelongsTo<ComponentType, $this>
     */
    public function componentType(): BelongsTo
    {
        return $this->belongsTo(ComponentType::class);
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_components')
            ->withPivot('quantity', 'sort_order')
            ->withTimestamps();
    }

    public function getFormattedPriceAttribute(): float
    {
        return round($this->current_unit_price_minor / 100, 2);
    }

    /**
     * Actualiza el precio y registra historial.
     */
    public function updatePrice(int $newPriceMinor, ?string $notes = null): void
    {
        $this->priceHistories()->create([
            'tenant_id' => $this->tenant_id,
            'unit_price_minor' => $this->current_unit_price_minor,
            'currency' => $this->currency,
            'effective_at' => now(),
            'notes' => $notes,
        ]);

        $this->update(['current_unit_price_minor' => $newPriceMinor]);

        if ($this->tenant?->shouldAutoRecalculate()) {
            RecalculateProductsForComponentJob::dispatch($this->id);
        }
    }
}
