<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('champion_item_stats', function (Blueprint $table) {
            $table->id();
            $table->string('campeao');
            $table->string('posicao')->nullable();
            $table->string('patch');
            $table->json('itens');
            $table->unsignedInteger('partidas')->default(0);
            $table->unsignedInteger('vitorias')->default(0);
            $table->decimal('winrate', 5, 2)->default(0);
            $table->timestamps();

            $table->index(['campeao', 'patch']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('champion_item_stats');
    }
};
