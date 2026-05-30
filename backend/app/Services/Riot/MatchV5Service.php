<?php

namespace App\Services\Riot;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MatchV5Service
{
    private string $apiKey;
    private string $routingRegion;

    public function __construct()
    {
        $this->apiKey = (string) config('services.riot.api_key');
        $this->routingRegion = (string) config('services.riot.routing_region', 'americas');
    }

    public function matchIdsByPuuid(string $puuid, int $start = 0, int $count = 20): array
    {
        $url = sprintf(
            'https://%s.api.riotgames.com/lol/match/v5/matches/by-puuid/%s/ids',
            $this->routingRegion,
            $puuid
        );

        return $this->request()
            ->get($url, ['start' => $start, 'count' => $count])
            ->throw()
            ->json();
    }

    public function matchById(string $matchId): array
    {
        $url = sprintf(
            'https://%s.api.riotgames.com/lol/match/v5/matches/%s',
            $this->routingRegion,
            $matchId
        );

        return $this->request()
            ->get($url)
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
        ]);
    }
}
