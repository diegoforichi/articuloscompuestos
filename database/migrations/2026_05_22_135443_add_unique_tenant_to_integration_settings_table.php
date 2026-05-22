<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicatedTenantIds = DB::table('integration_settings')
            ->select('tenant_id')
            ->whereNotNull('tenant_id')
            ->groupBy('tenant_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('tenant_id');

        if ($duplicatedTenantIds->isNotEmpty()) {
            throw new \RuntimeException(
                'No se puede aplicar unique(tenant_id) en integration_settings: hay tenants con múltiples integraciones (tenant_id: '.implode(', ', $duplicatedTenantIds->all()).').'
            );
        }

        Schema::table('integration_settings', function (Blueprint $table) {
            $table->unique('tenant_id', 'integration_settings_tenant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_settings', function (Blueprint $table) {
            $table->dropUnique('integration_settings_tenant_unique');
        });
    }
};
