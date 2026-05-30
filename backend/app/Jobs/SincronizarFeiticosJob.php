<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\FeiticoInvocador;
use App\Services\Riot\DataDragonService;

class SincronizarFeiticosJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DataDragonService $dataDragon)
    {
        $dados = $dataDragon->summonerSpells();

        foreach ($dados['data'] as $spell) {
            FeiticoInvocador::updateOrCreate(
                ['riot_id' => $spell['id']],
                [
                    'riot_key' => $spell['key'],
                    'nome' => $spell['name'],
                    'descricao' => $spell['description'],
                    'cooldown' => $spell['cooldown'][0] ?? null,
                    'modos' => $spell['modes'] ?? [],
                    'imagem' => $spell['image']['full'],
                    'versao' => $dados['version'],
                ]
            );
        }
    }
}
