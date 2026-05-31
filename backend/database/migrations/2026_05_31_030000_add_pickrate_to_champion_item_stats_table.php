<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('champion_item_stats', function (Blueprint $table) {
            $table->decimal('pickrate', 5, 2)->default(0)->after('winrate');
        });
    }

    public function down(): void
    {
        Schema::table('champion_item_stats', function (Blueprint $table) {
            $table->dropColumn('pickrate');
        });
    }
};
