<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category_name', 50);
            $table->string('currency', 3)->default('UYU');
            $table->unsignedBigInteger('price_minor')->default(0);
            $table->unsignedTinyInteger('ind_fact_id')->default(3);
            $table->unsignedTinyInteger('article_type_id')->default(1);
            $table->unsignedBigInteger('external_id')->nullable();
            $table->string('sync_status', 20)->default('draft');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
