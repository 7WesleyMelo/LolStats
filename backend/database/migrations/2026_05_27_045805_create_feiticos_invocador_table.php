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
        Schema::create('feiticos_invocador', function (Blueprint $table) {
            $table->id();

            $table->string('riot_id')->unique();
            $table->string('riot_key');

            $table->string('nome');
            $table->text('descricao')->nullable();

            $table->integer('cooldown')->nullable();

            $table->json('modos')->nullable();

            $table->string('imagem')->nullable();
            $table->string('versao');

            $table->timestamps();
        });
    }

    /**docker-compose exec app php artisan migrate
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feiticos_invocador');
    }
};
