<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductComponent extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'component_id',
        'quantity',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ProductComponent $productComponent) {
            if ($productComponent->tenant_id === null && $productComponent->product_id) {
                $productTenantId = Product::query()
                    ->whereKey($productComponent->product_id)
                    ->value('tenant_id');

                $productComponent->tenant_id = $productTenantId;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
