<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->string('environment_name');
            $table->string('base_url');
            $table->text('token');
            $table->string('rut_emisor');
            $table->string('default_category_name', 50)->nullable();
            $table->string('default_prefix', 20)->nullable();
            $table->string('remote_filter_mode', 20)->default('category');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_settings');
    }
};
