<?php

namespace App\Console\Commands;

use App\Jobs\SincronizarCampeoesJob;
use App\Jobs\SincronizarFeiticosJob;
use App\Jobs\SincronizarItensJob;
use App\Jobs\SincronizarRunasJob;
use App\Models\Patch;
use App\Services\Riot\DataDragonService;
use Illuminate\Console\Command;
use Throwable;

class SincronizarEstaticosCommand extends Command
{
    protected $signature = 'lol:sincronizar-estaticos';

    protected $description = 'Sincroniza dados estaticos do LoL (campeoes, itens, feiticos e runas).';

    public function handle(DataDragonService $dataDragon): int
    {
        $this->info('Iniciando sincronizacao de dados estaticos...');

        try {
            (new SincronizarCampeoesJob())->handle($dataDragon);
            (new SincronizarItensJob())->handle($dataDragon);
            (new SincronizarFeiticosJob())->handle($dataDragon);
            (new SincronizarRunasJob())->handle($dataDragon);

            $versaoAtual = $dataDragon->latestVersion();
            Patch::query()->update(['ativa' => false]);
            Patch::updateOrCreate(
                ['versao' => $versaoAtual],
                ['ativa' => true]
            );

            $this->info("Sincronizacao finalizada com sucesso. Patch ativo: {$versaoAtual}");
        } catch (Throwable $e) {
            $this->error('Falha ao sincronizar dados estaticos: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
