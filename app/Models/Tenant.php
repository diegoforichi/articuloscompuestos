<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Tenant extends Model
{
    use HasFactory;

    public const DEFAULT_SLUG = 'default';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'recalculation_mode',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function integrationSettings(): HasMany
    {
        return $this->hasMany(IntegrationSetting::class);
    }

    public function shouldAutoRecalculate(): bool
    {
        return $this->recalculation_mode === 'automatic';
    }

    /**
     * Tenant operativo base (slug `default`). Si no existe, usa el primer tenant activo.
     */
    public static function resolveDefaultTenantId(): ?int
    {
        $defaultId = static::query()
            ->where('slug', self::DEFAULT_SLUG)
            ->where('is_active', true)
            ->value('id');

        if ($defaultId !== null) {
            return (int) $defaultId;
        }

        $fallbackId = static::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');

        return $fallbackId !== null ? (int) $fallbackId : null;
    }

    /**
     * Tenant de contexto para consultas y altas: usuario con tenant_id, o default para superadmin.
     */
    public static function resolveCurrentTenantId(): ?int
    {
        $user = Auth::user();

        if ($user && $user->tenant_id) {
            $tenantId = (int) $user->tenant_id;
            $isActive = static::query()
                ->whereKey($tenantId)
                ->where('is_active', true)
                ->exists();

            return $isActive ? $tenantId : null;
        }

        return static::resolveDefaultTenantId();
    }
}
