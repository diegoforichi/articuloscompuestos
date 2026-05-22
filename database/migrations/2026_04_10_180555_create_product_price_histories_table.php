<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('price_minor');
            $table->string('currency', 3);
            $table->timestamp('calculated_at');
            $table->json('breakdown_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_price_histories');
    }
};
