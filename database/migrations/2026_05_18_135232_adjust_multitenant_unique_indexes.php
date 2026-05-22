<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropUnique('components_code_unique');
            $table->unique(['tenant_id', 'code'], 'components_tenant_code_unique');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_code_unique');
            $table->unique(['tenant_id', 'code'], 'products_tenant_code_unique');
        });

        Schema::table('component_types', function (Blueprint $table) {
            $table->dropUnique('component_types_code_unique');
            $table->unique(['tenant_id', 'code'], 'component_types_tenant_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropUnique('components_tenant_code_unique');
            $table->unique('code');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_tenant_code_unique');
            $table->unique('code');
        });

        Schema::table('component_types', function (Blueprint $table) {
            $table->dropUnique('component_types_tenant_code_unique');
            $table->unique('code');
        });
    }
};
