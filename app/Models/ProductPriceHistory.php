<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'cost_minor',
        'margin_percent',
        'price_minor',
        'currency',
        'calculated_at',
        'breakdown_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'cost_minor' => 'integer',
            'margin_percent' => 'decimal:2',
            'price_minor' => 'integer',
            'calculated_at' => 'datetime',
            'breakdown_snapshot' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
