<?php

namespace App\Jobs;

use App\Models\Summoner;
use App\Services\Riot\AccountService;
use App\Services\Riot\RankedService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Throwable;

class SeedRankedPlayersJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $diamondPages = 3
    ) {
    }

    public function handle(RankedService $rankedService, AccountService $accountService): void
    {
        $grupos = [];
        $grupos[] = $rankedService->challengerLeague()['entries'] ?? [];
        $grupos[] = $rankedService->grandmasterLeague()['entries'] ?? [];
        $grupos[] = $rankedService->masterLeague()['entries'] ?? [];

        for ($p = 1; $p <= $this->diamondPages; $p++) {
            $grupos[] = $rankedService->diamondOneEntries($p);
        }

        foreach ($grupos as $entries) {
            foreach ($entries as $entry) {
                try {
                    $summonerId = (string) ($entry['summonerId'] ?? '');
                    if ($summonerId === '') {
                        continue;
                    }

                    $puuid = Arr::get($entry, 'puuid');
                    if (is_string($puuid) && $puuid === '') {
                        $puuid = null;
                    }

                    $accountData = [];
                    if (is_string($puuid) && $puuid !== '') {
                        $accountData = $accountService->buscarPorPuuid($puuid);
                    }

                    Summoner::updateOrCreate(
                        ['summoner_id' => $summonerId],
                        [
                            'puuid' => $puuid,
                            'nome' => $accountData['gameName'] ?? ($entry['summonerName'] ?? null),
                            'tag_line' => $accountData['tagLine'] ?? null,
                            'tier' => $entry['tier'] ?? null,
                            'rank' => $entry['rank'] ?? null,
                            'league_points' => (int) ($entry['leaguePoints'] ?? 0),
                            'wins' => (int) ($entry['wins'] ?? 0),
                            'losses' => (int) ($entry['losses'] ?? 0),
                            'region' => (string) config('services.riot.region', 'br1'),
                            'last_seeded_at' => now(),
                        ]
                    );
                } catch (Throwable) {
                    // Continua a coleta mesmo se um jogador falhar.
                    continue;
                }
            }
        }
    }
}
