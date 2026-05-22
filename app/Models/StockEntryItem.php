<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_entry_id',
        'component_id',
        'quantity',
        'unit_cost_minor',
        'subtotal_minor',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_cost_minor' => 'integer',
            'subtotal_minor' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<StockEntry, $this>
     */
    public function stockEntry(): BelongsTo
    {
        return $this->belongsTo(StockEntry::class);
    }

    /**
     * @return BelongsTo<Component, $this>
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
