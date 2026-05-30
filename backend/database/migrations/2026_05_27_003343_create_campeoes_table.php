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
        Schema::create('campeoes', function (Blueprint $table) {
            $table->id();

            $table->string('riot_id'); // Ahri
            $table->string('nome');
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();

            $table->string('imagem');

            $table->string('versao');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campeoes');
    }
};
