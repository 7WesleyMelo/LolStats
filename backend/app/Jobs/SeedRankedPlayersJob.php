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

        $challenger = $rankedService->challengerLeague();
        $grupos[] = [
            'entries' => $challenger['entries'] ?? [],
            'tier' => $challenger['tier'] ?? 'CHALLENGER',
        ];

        $grandmaster = $rankedService->grandmasterLeague();
        $grupos[] = [
            'entries' => $grandmaster['entries'] ?? [],
            'tier' => $grandmaster['tier'] ?? 'GRANDMASTER',
        ];

        $master = $rankedService->masterLeague();
        $grupos[] = [
            'entries' => $master['entries'] ?? [],
            'tier' => $master['tier'] ?? 'MASTER',
        ];

        for ($p = 1; $p <= $this->diamondPages; $p++) {
            $grupos[] = [
                'entries' => $rankedService->diamondOneEntries($p),
                'tier' => 'DIAMOND',
            ];
        }

        foreach ($grupos as $grupo) {
            $entries = $grupo['entries'] ?? [];
            $groupTier = $grupo['tier'] ?? null;
            foreach ($entries as $entry) {
                try {
                    $summonerId = (string) ($entry['summonerId'] ?? '');
                    $puuid = Arr::get($entry, 'puuid');
                    if (is_string($puuid) && $puuid === '') {
                        $puuid = null;
                    }
                    if ($summonerId === '' && !is_string($puuid)) {
                        continue;
                    }

                    $accountData = [];
                    if (is_string($puuid) && $puuid !== '') {
                        $accountData = $accountService->buscarPorPuuid($puuid);
                    }

                    $attributes = [
                        'puuid' => $puuid,
                        'summoner_id' => $summonerId !== '' ? $summonerId : null,
                        'nome' => $accountData['gameName'] ?? ($entry['summonerName'] ?? null),
                        'tag_line' => $accountData['tagLine'] ?? null,
                        'tier' => $entry['tier'] ?? $groupTier,
                        'rank' => $entry['rank'] ?? null,
                        'league_points' => (int) ($entry['leaguePoints'] ?? 0),
                        'wins' => (int) ($entry['wins'] ?? 0),
                        'losses' => (int) ($entry['losses'] ?? 0),
                        'region' => (string) config('services.riot.region', 'br1'),
                        'last_seeded_at' => now(),
                    ];

                    if (is_string($puuid) && $puuid !== '') {
                        Summoner::updateOrCreate(['puuid' => $puuid], $attributes);
                    } else {
                        Summoner::updateOrCreate(['summoner_id' => $summonerId], $attributes);
                    }
                } catch (Throwable) {
                    // Continua a coleta mesmo se um jogador falhar.
                    continue;
                }
            }
        }
    }
}
