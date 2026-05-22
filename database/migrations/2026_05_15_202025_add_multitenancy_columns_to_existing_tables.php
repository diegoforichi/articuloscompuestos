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
        $defaultTenantId = (int) DB::table('tenants')
            ->where('slug', 'default')
            ->value('id');

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->boolean('is_super_admin')->default(false)->after('password');
        });

        Schema::table('component_types', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'code']);
        });

        Schema::table('components', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'code']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'code']);
        });

        Schema::table('integration_settings', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'is_active']);
        });

        Schema::table('product_components', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'product_id']);
        });

        Schema::table('component_price_histories', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'component_id']);
        });

        Schema::table('product_price_histories', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'product_id']);
        });

        Schema::table('sync_logs', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->index(['tenant_id', 'product_id']);
        });

        DB::table('users')
            ->whereNull('tenant_id')
            ->update([
                'tenant_id' => $defaultTenantId,
                'is_super_admin' => true,
            ]);

        DB::table('component_types')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('components')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('products')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('integration_settings')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('product_components')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('component_price_histories')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('product_price_histories')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
        DB::table('sync_logs')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_logs', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'product_id']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('product_price_histories', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'product_id']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('component_price_histories', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'component_id']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('product_components', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'product_id']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('integration_settings', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'is_active']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'code']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('components', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'code']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('component_types', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'code']);
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn('is_super_admin');
        });
    }
};
