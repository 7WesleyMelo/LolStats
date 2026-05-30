<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('champion_stats', function (Blueprint $table) {
            $table->decimal('pickrate', 5, 2)->default(0)->after('winrate');
            $table->decimal('kda', 8, 2)->default(0)->after('pickrate');
            $table->decimal('kills_medias', 8, 2)->default(0)->after('kda');
            $table->decimal('deaths_medias', 8, 2)->default(0)->after('kills_medias');
            $table->decimal('assists_medias', 8, 2)->default(0)->after('deaths_medias');
            $table->decimal('farm_medio', 8, 2)->default(0)->after('assists_medias');
            $table->decimal('gold_medio', 12, 2)->default(0)->after('farm_medio');
            $table->decimal('damage_medio', 12, 2)->default(0)->after('gold_medio');
        });

        Schema::table('champion_item_stats', function (Blueprint $table) {
            $table->unsignedInteger('item')->nullable()->after('patch');
            $table->index(['campeao', 'posicao', 'patch', 'item'], 'champion_item_stats_lookup_idx');
        });

        Schema::table('champion_rune_stats', function (Blueprint $table) {
            $table->string('runa')->nullable()->after('patch');
            $table->index(['campeao', 'posicao', 'patch', 'runa'], 'champion_rune_stats_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('champion_rune_stats', function (Blueprint $table) {
            $table->dropIndex('champion_rune_stats_lookup_idx');
            $table->dropColumn('runa');
        });

        Schema::table('champion_item_stats', function (Blueprint $table) {
            $table->dropIndex('champion_item_stats_lookup_idx');
            $table->dropColumn('item');
        });

        Schema::table('champion_stats', function (Blueprint $table) {
            $table->dropColumn([
                'pickrate',
                'kda',
                'kills_medias',
                'deaths_medias',
                'assists_medias',
                'farm_medio',
                'gold_medio',
                'damage_medio',
            ]);
        });
    }
};
