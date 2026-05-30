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
        Schema::create('matches', function (Blueprint $table) {

            $table->id();

            $table->string('match_id')
                ->unique();

            $table->string('riot_match_id')
                ->unique();

            $table->string('versao');

            $table->string('queue_id');

            $table->integer('duracao');

            $table->timestamp('jogada_em');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
