<?php

namespace App\Jobs;

use App\Models\ArvoreRuna;
use App\Models\Runa;
use App\Services\Riot\DataDragonService;

class SincronizarRunasJob
{
    public function handle(DataDragonService $dataDragon): void
    {
        $dados = $dataDragon->runes();

        foreach ($dados as $tree) {
            $arvore = ArvoreRuna::updateOrCreate(
                ['riot_id' => $tree['id']],
                [
                    'nome' => $tree['name'],
                    'chave' => $tree['key'],
                    'icone' => $tree['icon'],
                ]
            );

            foreach ($tree['slots'] as $slotIndex => $slot) {
                foreach ($slot['runes'] as $rune) {
                    Runa::updateOrCreate(
                        ['riot_id' => $rune['id']],
                        [
                            'arvore_runa_id' => $arvore->id,
                            'slot' => $slotIndex,
                            'nome' => $rune['name'],
                            'descricao' => $rune['longDesc'],
                            'icone' => $rune['icon'],
                        ]
                    );
                }
            }
        }
    }
}
