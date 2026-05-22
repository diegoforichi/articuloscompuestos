<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->unsignedBigInteger('component_type_id')->nullable()->after('code');
        });

        $idByCode = DB::table('component_types')->pluck('id', 'code')->all();

        $components = DB::table('components')->select('id', 'type')->get();

        foreach ($components as $row) {
            $raw = strtolower(trim((string) $row->type));
            $code = match ($raw) {
                'metal', 'gem', 'labor', 'other' => $raw,
                default => 'other',
            };

            if (! isset($idByCode[$code])) {
                $code = 'other';
            }

            DB::table('components')->where('id', $row->id)->update([
                'component_type_id' => $idByCode[$code],
            ]);
        }

        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('components', function (Blueprint $table) {
            $table->foreign('component_type_id')
                ->references('id')
                ->on('component_types')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropForeign(['component_type_id']);
        });

        Schema::table('components', function (Blueprint $table) {
            $table->string('type')->after('code')->nullable();
        });

        $idToCode = DB::table('component_types')->pluck('code', 'id')->all();

        $components = DB::table('components')->select('id', 'component_type_id')->get();

        foreach ($components as $row) {
            $code = $idToCode[$row->component_type_id] ?? 'other';
            $legacy = match ($code) {
                'metal' => 'Metal',
                'gem' => 'Gem',
                'labor' => 'Labor',
                default => 'Other',
            };

            DB::table('components')->where('id', $row->id)->update([
                'type' => $legacy,
            ]);
        }

        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('component_type_id');
        });
    }
};
