<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_participantes', function (Blueprint $table) {
            $table->string('team_position')->nullable()->after('lane');
        });
    }

    public function down(): void
    {
        Schema::table('match_participantes', function (Blueprint $table) {
            $table->dropColumn('team_position');
        });
    }
};
