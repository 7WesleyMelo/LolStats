<?php

namespace App\Services\Riot;

use Illuminate\Support\Facades\Http;

class AccountService
{
    public function buscarPorRiotId(string $gameName, string $tagLine): array
    {
        $routing = config('services.riot.routing_region', 'americas');
        $apiKey = config('services.riot.api_key');

        return Http::withHeaders([
            'X-Riot-Token' => $apiKey,
        ])->get("https://{$routing}.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}")
            ->throw()
            ->json();
    }

    public function buscarPorPuuid(string $puuid): array
    {
        $routing = config('services.riot.routing_region', 'americas');
        $apiKey = config('services.riot.api_key');

        return Http::withHeaders([
            'X-Riot-Token' => $apiKey,
        ])->get("https://{$routing}.api.riotgames.com/riot/account/v1/accounts/by-puuid/{$puuid}")
            ->throw()
            ->json();
    }
}
