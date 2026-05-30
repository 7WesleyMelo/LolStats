<?php

namespace App\Console\Commands;

use App\Jobs\SeedRankedPlayersJob;
use Illuminate\Console\Command;
use Throwable;

class SeedRankedPlayersCommand extends Command
{
    protected $signature = 'lol:seed-ranked-players {--pages=3}';

    protected $description = 'Coleta jogadores ranqueados (Diamond+ até Challenger) para a tabela summoners.';

    public function handle(): int
    {
        try {
            SeedRankedPlayersJob::dispatchSync((int) $this->option('pages'));
            $this->info('Seed de jogadores ranqueados concluido.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Falha no seed de jogadores: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
