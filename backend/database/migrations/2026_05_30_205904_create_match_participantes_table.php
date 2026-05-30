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
        Schema::create('match_participantes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('match_id')
                ->constrained('matches')
                ->cascadeOnDelete();

            $table->string('puuid');

            $table->string('campeao');

            $table->string('lane')->nullable();

            $table->string('role')->nullable();

            $table->boolean('venceu');

            $table->integer('kills');
            $table->integer('deaths');
            $table->integer('assists');

            $table->integer('gold');

            $table->integer('farm');

            $table->integer('dano_total');

            $table->json('itens');

            $table->json('runas');

            $table->json('feiticos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_participantes');
    }
};
