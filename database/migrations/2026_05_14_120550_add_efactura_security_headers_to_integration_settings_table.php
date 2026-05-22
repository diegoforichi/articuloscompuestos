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
        Schema::table('integration_settings', function (Blueprint $table) {
            $table->text('auth_header_value')->nullable()->after('rut_emisor');
            $table->string('origin_url', 512)->nullable()->after('auth_header_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_settings', function (Blueprint $table) {
            $table->dropColumn(['auth_header_value', 'origin_url']);
        });
    }
};
