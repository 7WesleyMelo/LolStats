<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class RiotApiService
{
    private string $baseUrl = 'https://ddragon.leagueoflegends.com';

    /**
     * @throws ConnectionException
     */
    public function getVersions()
    {
        return Http::get("$this->baseUrl/api/versions.json")->json();
    }

    /**
     * @throws ConnectionException
     */
    /** @noinspection PhpUnused */
    public function getChampions(string $version)
    {
        return Http::get("$this->baseUrl/cdn/$version/data/en_US/champion.json")->json();
    }
}
