<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_settings', function (Blueprint $table) {
            $table->decimal('default_margin_percent', 5, 2)->default(0)->after('default_prefix');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('cost_minor')->default(0)->after('currency');
            $table->decimal('margin_percent', 5, 2)->default(0)->after('cost_minor');
        });

        Schema::table('product_price_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('cost_minor')->default(0)->after('product_id');
            $table->decimal('margin_percent', 5, 2)->default(0)->after('cost_minor');
        });

        DB::table('products')->update([
            'cost_minor' => DB::raw('price_minor'),
            'margin_percent' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::table('product_price_histories', function (Blueprint $table) {
            $table->dropColumn(['cost_minor', 'margin_percent']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_minor', 'margin_percent']);
        });

        Schema::table('integration_settings', function (Blueprint $table) {
            $table->dropColumn('default_margin_percent');
        });
    }
};
