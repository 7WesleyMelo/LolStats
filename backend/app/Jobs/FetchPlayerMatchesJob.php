<?php

namespace App\Jobs;

use App\Models\MatchModel;
use App\Models\Summoner;
use App\Services\Riot\MatchV5Service;
use App\Services\Riot\RankedService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class FetchPlayerMatchesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $jogadores = 100,
        public int $partidasPorJogador = 20
    ) {
    }

    public function handle(MatchV5Service $matchV5, RankedService $rankedService): void
    {
        $limiteJogadores = max(1, min(1000, $this->jogadores));
        $limitePartidas = max(1, min(100, $this->partidasPorJogador));

        $summoners = Summoner::query()
            ->orderByRaw('last_matches_fetched_at IS NULL DESC')
            ->orderBy('last_matches_fetched_at')
            ->limit($limiteJogadores)
            ->get();

        foreach ($summoners as $summoner) {
            try {
                if (!$summoner->puuid && $summoner->summoner_id) {
                    $summonerData = $rankedService->summonerById($summoner->summoner_id);
                    $summoner->update([
                        'puuid' => $summonerData['puuid'] ?? null,
                    ]);
                }

                if (!$summoner->puuid) {
                    continue;
                }

                $matchIds = $matchV5->matchIdsByPuuid($summoner->puuid, 0, $limitePartidas);

                foreach ($matchIds as $matchId) {
                    if (!MatchModel::query()->where('riot_match_id', $matchId)->exists()) {
                        ProcessMatchJob::dispatch($matchId);
                    }
                }

                $summoner->update([
                    'last_matches_fetched_at' => now(),
                ]);
            } catch (Throwable) {
                continue;
            }
        }
    }
}
