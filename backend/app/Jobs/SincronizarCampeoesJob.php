<?php

namespace App\Jobs;

use App\Models\Campeao;
use App\Services\Riot\DataDragonService;

class SincronizarCampeoesJob
{
    public function handle(DataDragonService $dataDragon)
    {
        $dados = $dataDragon->champions();

        foreach ($dados['data'] as $champ) {
            Campeao::updateOrCreate(
                ['riot_id' => $champ['id']],
                [
                    'riot_key' => $champ['key'],
                    'nome' => $champ['name'],
                    'titulo' => $champ['title'],
                    'descricao' => $champ['blurb'],
                    'imagem' => $champ['image']['full'],
                    'tags' => $champ['tags'] ?? [],
                    'info' => $champ['info'] ?? [],
                    'stats' => $champ['stats'] ?? [],
                    'tipo_recurso' => $champ['partype'] ?? null,
                    'versao' => $dados['version'],
                ]
            );
        }
    }
}
