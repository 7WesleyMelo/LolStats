<?php

namespace App\Services\Riot;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RankedService
{
    private string $apiKey;
    private string $platformRegion;

    public function __construct()
    {
        $this->apiKey = (string) config('services.riot.api_key');
        $this->platformRegion = (string) config('services.riot.region', 'br1');
    }

    public function challengerLeague(): array
    {
        return $this->request()->get($this->platformUrl('/lol/league/v4/challengerleagues/by-queue/RANKED_SOLO_5x5'))
            ->throw()
            ->json();
    }

    public function grandmasterLeague(): array
    {
        return $this->request()->get($this->platformUrl('/lol/league/v4/grandmasterleagues/by-queue/RANKED_SOLO_5x5'))
            ->throw()
            ->json();
    }

    public function masterLeague(): array
    {
        return $this->request()->get($this->platformUrl('/lol/league/v4/masterleagues/by-queue/RANKED_SOLO_5x5'))
            ->throw()
            ->json();
    }

    public function diamondOneEntries(int $page = 1): array
    {
        return $this->request()->get($this->platformUrl('/lol/league-exp/v4/entries/RANKED_SOLO_5x5/DIAMOND/I'), [
            'page' => $page,
        ])->throw()->json();
    }

    public function summonerById(string $summonerId): array
    {
        return $this->request()->get($this->platformUrl('/lol/summoner/v4/summoners/'.$summonerId))
            ->throw()
            ->json();
    }

    private function request(): PendingRequest
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('RIOT_API_KEY nao configurada.');
        }

        return Http::withHeaders([
            'X-Riot-Token' => $this->apiKey,
        ])->timeout(30);
    }

    private function platformUrl(string $path): string
    {
        return sprintf('https://%s.api.riotgames.com%s', $this->platformRegion, $path);
    }
}
