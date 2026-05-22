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
        Schema::create('stock_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_entry_id')->constrained('stock_entries')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('components')->restrictOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->unsignedBigInteger('unit_cost_minor');
            $table->unsignedBigInteger('subtotal_minor');
            $table->timestamps();

            $table->index(['stock_entry_id', 'component_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entry_items');
    }
};
