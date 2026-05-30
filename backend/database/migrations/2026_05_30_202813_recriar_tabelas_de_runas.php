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
        Schema::dropIfExists('runas');
        Schema::dropIfExists('arvores_runas');

        Schema::create('arvores_runas', function (Blueprint $table) {
            $table->id();
            $table->integer('riot_id')->unique();
            $table->string('nome');
            $table->string('chave');
            $table->string('icone')->nullable();
            $table->timestamps();
        });

        Schema::create('runas', function (Blueprint $table) {
            $table->id();
            $table->integer('riot_id')->unique();

            $table->foreignId('arvore_runa_id')
                ->constrained('arvores_runas')
                ->cascadeOnDelete();

            $table->integer('slot');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('icone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('runas');
        Schema::dropIfExists('arvores_runas');
    }
};
