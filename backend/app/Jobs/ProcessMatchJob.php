<?php

namespace App\Jobs;

use App\Models\MatchModel;
use App\Models\MatchParticipante;
use App\Services\Riot\MatchV5Service;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessMatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $riotMatchId
    ) {
    }

    public function handle(MatchV5Service $matchV5): void
    {
        if (MatchModel::query()->where('riot_match_id', $this->riotMatchId)->exists()) {
            return;
        }

        $dadosMatch = $matchV5->matchById($this->riotMatchId);
        $info = $dadosMatch['info'] ?? [];

        DB::transaction(function () use ($info): void {
            $match = MatchModel::create([
                'match_id' => $this->riotMatchId,
                'riot_match_id' => $this->riotMatchId,
                'versao' => (string) ($info['gameVersion'] ?? ''),
                'queue_id' => (string) ($info['queueId'] ?? ''),
                'duracao' => (int) ($info['gameDuration'] ?? 0),
                'jogada_em' => now()->setTimestamp((int) floor(($info['gameEndTimestamp'] ?? 0) / 1000)),
            ]);

            foreach (($info['participants'] ?? []) as $p) {
                MatchParticipante::create([
                    'match_id' => $match->id,
                    'puuid' => (string) ($p['puuid'] ?? ''),
                    'campeao' => (string) ($p['championName'] ?? ''),
                    'lane' => $p['lane'] ?? null,
                    'team_position' => $p['teamPosition'] ?? null,
                    'role' => $p['role'] ?? null,
                    'venceu' => (bool) ($p['win'] ?? false),
                    'kills' => (int) ($p['kills'] ?? 0),
                    'deaths' => (int) ($p['deaths'] ?? 0),
                    'assists' => (int) ($p['assists'] ?? 0),
                    'gold' => (int) ($p['goldEarned'] ?? 0),
                    'farm' => (int) (($p['totalMinionsKilled'] ?? 0) + ($p['neutralMinionsKilled'] ?? 0)),
                    'dano_total' => (int) ($p['totalDamageDealtToChampions'] ?? 0),
                    'itens' => [
                        $p['item0'] ?? 0,
                        $p['item1'] ?? 0,
                        $p['item2'] ?? 0,
                        $p['item3'] ?? 0,
                        $p['item4'] ?? 0,
                        $p['item5'] ?? 0,
                        $p['item6'] ?? 0,
                    ],
                    'runas' => $p['perks'] ?? [],
                    'feiticos' => [
                        $p['summoner1Id'] ?? null,
                        $p['summoner2Id'] ?? null,
                    ],
                ]);
            }
        });
    }
}
