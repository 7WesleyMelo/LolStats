<?php

namespace App\Console\Commands;

use App\Jobs\FetchPlayerMatchesJob;
use Illuminate\Console\Command;
use Throwable;

class FetchPlayerMatchesCommand extends Command
{
    protected $signature = 'lol:fetch-player-matches {--jogadores=100} {--partidas=20}';

    protected $description = 'Busca partidas recentes dos jogadores seedados e enfileira processamento.';

    public function handle(): int
    {
        try {
            FetchPlayerMatchesJob::dispatchSync(
                (int) $this->option('jogadores'),
                (int) $this->option('partidas')
            );

            $this->info('Busca de partidas disparada com sucesso.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Falha ao buscar partidas: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
