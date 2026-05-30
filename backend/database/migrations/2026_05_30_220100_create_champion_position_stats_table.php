<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('champion_position_stats', function (Blueprint $table) {
            $table->id();
            $table->string('campeao');
            $table->string('posicao');
            $table->string('patch');
            $table->unsignedInteger('partidas')->default(0);
            $table->unsignedInteger('vitorias')->default(0);
            $table->decimal('winrate', 5, 2)->default(0);
            $table->decimal('pickrate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['campeao', 'posicao', 'patch']);
            $table->index(['patch', 'partidas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('champion_position_stats');
    }
};
