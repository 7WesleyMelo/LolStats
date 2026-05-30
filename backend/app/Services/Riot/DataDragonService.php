<?php

namespace App\Services\Riot;

use Illuminate\Support\Facades\Http;

class DataDragonService
{
    private string $baseUrl;
    private string $locale;

    public function __construct()
    {
        $this->baseUrl = config('services.riot.data_dragon_url');
        $this->locale = config('services.riot.locale', 'pt_BR');
    }

    public function versions(): array
    {
        return Http::get("$this->baseUrl/api/versions.json")->json();
    }

    public function latestVersion(): string
    {
        return $this->versions()[0];
    }

    public function champions(?string $version = null): array
    {
        $version ??= $this->latestVersion();

        return Http::get(
            "$this->baseUrl/cdn/$version/data/$this->locale/champion.json"
        )->json();
    }

    public function items(?string $version = null): array
    {
        $version ??= $this->latestVersion();

        return Http::get(
            "$this->baseUrl/cdn/$version/data/$this->locale/item.json"
        )->json();
    }

    public function summonerSpells(?string $version = null): array
    {
        $version ??= $this->latestVersion();

        return Http::get(
            "$this->baseUrl/cdn/$version/data/$this->locale/summoner.json"
        )->json();
    }

    public function runes(): array
    {
        $version = $this->latestVersion();

        return Http::get(
            "$this->baseUrl/cdn/$version/data/$this->locale/runesReforged.json"
        )->json();
    }
}
