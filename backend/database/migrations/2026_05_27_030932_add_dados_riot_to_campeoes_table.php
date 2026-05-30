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
        Schema::table('campeoes', function (Blueprint $table) {
            $table->string('riot_key')->nullable()->after('riot_id');
            $table->json('tags')->nullable()->after('imagem');
            $table->json('info')->nullable()->after('tags');
            $table->json('stats')->nullable()->after('info');
            $table->string('tipo_recurso')->nullable()->after('stats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campeoes', function (Blueprint $table) {
            //
        });
    }
};
