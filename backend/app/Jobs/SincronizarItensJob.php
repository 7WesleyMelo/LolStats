<?php

namespace App\Jobs;

use App\Models\Item;
use App\Services\Riot\DataDragonService;

class SincronizarItensJob
{
    public function handle(DataDragonService $dataDragon): void
    {
        $dados = $dataDragon->items();

        foreach ($dados['data'] as $riotId => $item) {
            Item::updateOrCreate(
                ['riot_id' => $riotId],
                [
                    'nome' => $item['name'] ?? '',
                    'descricao' => $item['description'] ?? null,
                    'descricao_simples' => $item['plaintext'] ?? null,

                    'preco_base' => $item['gold']['base'] ?? 0,
                    'preco_total' => $item['gold']['total'] ?? 0,
                    'preco_venda' => $item['gold']['sell'] ?? 0,
                    'compravel' => $item['gold']['purchasable'] ?? false,

                    'tags' => $item['tags'] ?? [],
                    'mapas' => $item['maps'] ?? [],
                    'stats' => $item['stats'] ?? [],
                    'from' => $item['from'] ?? [],
                    'into' => $item['into'] ?? [],

                    'imagem' => $item['image']['full'] ?? null,
                    'versao' => $dados['version'],
                ]
            );
        }
    }
}
