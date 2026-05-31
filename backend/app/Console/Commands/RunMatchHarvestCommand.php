<?php

namespace App\Console\Commands;

use App\Jobs\FetchPlayerMatchesJob;
use App\Models\MatchModel;
use App\Models\Summoner;
use Illuminate\Console\Command;
use Throwable;

class RunMatchHarvestCommand extends Command
{
    protected $signature = 'lol:run-match-harvest
                            {--cycles=50 : Quantidade de ciclos}
                            {--jogadores=500 : Summoners por ciclo}
                            {--partidas=20 : Partidas por summoner}
                            {--sleep=8 : Espera entre ciclos em segundos}
                            {--target=0 : Meta de partidas (0 desabilita)}';

    protected $description = 'Executa coleta de partidas em ciclos, com progresso, para escalar volume de dados.';

    public function handle(): int
    {
        $cycles = max(1, (int) $this->option('cycles'));
        $jogadores = max(1, min(1000, (int) $this->option('jogadores')));
        $partidas = max(1, min(100, (int) $this->option('partidas')));
        $sleep = max(0, (int) $this->option('sleep'));
        $target = max(0, (int) $this->option('target'));

        $this->line('Inicio do harvest');
        $this->line('summoners='.Summoner::query()->count().' matches='.MatchModel::query()->count());

        for ($cycle = 1; $cycle <= $cycles; $cycle++) {
            try {
                FetchPlayerMatchesJob::dispatchSync($jogadores, $partidas);
            } catch (Throwable $e) {
                $this->error("Ciclo {$cycle} falhou: ".$e->getMessage());
                return self::FAILURE;
            }

            $matches = MatchModel::query()->count();
            $this->line("ciclo={$cycle}/{$cycles} matches={$matches}");

            if ($target > 0 && $matches >= $target) {
                $this->info("Meta atingida: {$matches} partidas.");
                return self::SUCCESS;
            }

            if ($cycle < $cycles && $sleep > 0) {
                sleep($sleep);
            }
        }

        $this->info('Harvest concluido.');
        return self::SUCCESS;
    }
}

