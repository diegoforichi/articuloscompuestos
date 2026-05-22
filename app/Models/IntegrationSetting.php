<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationSetting extends Model
{
    protected $fillable = [
        'tenant_id',
        'environment_name',
        'base_url',
        'token',
        'rut_emisor',
        'auth_header_value',
        'origin_url',
        'default_category_name',
        'default_prefix',
        'default_margin_percent',
        'remote_filter_mode',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_margin_percent' => 'decimal:2',
            'is_active' => 'boolean',
            'token' => 'encrypted',
            'auth_header_value' => 'encrypted',
        ];
    }

    public function setDefaultMarginPercentAttribute(mixed $value): void
    {
        $numeric = is_numeric($value) ? (float) $value : 0.0;

        $this->attributes['default_margin_percent'] = (string) max(0.0, min(100.0, $numeric));
    }

    /**
     * Devuelve la configuración activa de integración.
     */
    public static function active(?int $tenantId = null): ?self
    {
        $tenantId ??= Tenant::resolveCurrentTenantId();

        return static::query()
            ->where('is_active', true)
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->first();
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected static function booted(): void
    {
        static::saving(function (IntegrationSetting $setting) {
            if ($setting->tenant_id === null) {
                $setting->tenant_id = Tenant::resolveCurrentTenantId();
            }

            if ($setting->is_active) {
                static::where('id', '!=', $setting->id ?? 0)
                    ->where('tenant_id', $setting->tenant_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }
}
