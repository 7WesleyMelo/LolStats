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
        Schema::create('itens', function (Blueprint $table) {
            $table->id();

            $table->string('riot_id')->unique(); // Ex: 1001
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->text('descricao_simples')->nullable();

            $table->integer('preco_base')->default(0);
            $table->integer('preco_total')->default(0);
            $table->integer('preco_venda')->default(0);
            $table->boolean('compravel')->default(false);

            $table->json('tags')->nullable();
            $table->json('mapas')->nullable();
            $table->json('stats')->nullable();
            $table->json('from')->nullable();
            $table->json('into')->nullable();

            $table->string('imagem')->nullable();
            $table->string('versao');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens');
    }
};
