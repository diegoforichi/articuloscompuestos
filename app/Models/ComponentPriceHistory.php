<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponentPriceHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'component_id',
        'unit_price_minor',
        'currency',
        'effective_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_minor' => 'integer',
            'effective_at' => 'datetime',
        ];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
