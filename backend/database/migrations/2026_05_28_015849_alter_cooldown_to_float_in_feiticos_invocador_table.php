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
        Schema::table('feiticos_invocador', function (Blueprint $table) {
            $table->decimal('cooldown', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('feiticos_invocador', function (Blueprint $table) {
            $table->integer('cooldown')->nullable()->change();
        });
    }
};
