<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summoners', function (Blueprint $table) {
            $table->id();
            $table->string('puuid')->unique();
            $table->string('summoner_id')->nullable()->unique();
            $table->string('nome')->nullable();
            $table->string('tag_line')->nullable();
            $table->string('tier')->nullable();
            $table->string('rank')->nullable();
            $table->integer('league_points')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->string('region')->default('br1');
            $table->timestamp('last_seeded_at')->nullable();
            $table->timestamp('last_matches_fetched_at')->nullable();
            $table->timestamps();

            $table->index(['tier', 'rank']);
            $table->index('last_matches_fetched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('summoners');
    }
};
