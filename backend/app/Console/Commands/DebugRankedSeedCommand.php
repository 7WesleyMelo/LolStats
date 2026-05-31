<?php

namespace App\Console\Commands;

use App\Services\Riot\RankedService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Throwable;

class DebugRankedSeedCommand extends Command
{
    protected $signature = 'lol:debug-ranked-seed {--pages=3} {--details}';

    protected $description = 'Debuga a coleta de ranked por tier e mostra contagens/erros da Riot API.';

    public function handle(RankedService $rankedService): int
    {
        $this->line('Regiao: '.(string) config('services.riot.region', 'br1'));

        $checks = [
            'Challenger' => fn () => count($rankedService->challengerLeague()['entries'] ?? []),
            'Grandmaster' => fn () => count($rankedService->grandmasterLeague()['entries'] ?? []),
            'Master' => fn () => count($rankedService->masterLeague()['entries'] ?? []),
            'Diamond' => function () use ($rankedService): int {
                $total = 0;
                for ($page = 1; $page <= (int) $this->option('pages'); $page++) {
                    $total += count($rankedService->diamondOneEntries($page));
                }

                return $total;
            },
        ];

        $hasError = false;

        foreach ($checks as $label => $callback) {
            try {
                $result = $callback();
                $this->line($label.': '.(string) $result);

                if ((bool) $this->option('details')) {
                    $this->printDetails($label, $rankedService);
                }
            } catch (Throwable $e) {
                $hasError = true;
                $this->line($label.': '.$this->formatError($e));
            }
        }

        return $hasError ? self::FAILURE : self::SUCCESS;
    }

    private function formatError(Throwable $e): string
    {
        if ($e instanceof RequestException && $e->response !== null) {
            return 'Erro '.$e->response->status();
        }

        return 'Erro '.$e->getMessage();
    }

    private function printDetails(string $label, RankedService $rankedService): void
    {
        $entries = match ($label) {
            'Challenger' => $rankedService->challengerLeague()['entries'] ?? [],
            'Grandmaster' => $rankedService->grandmasterLeague()['entries'] ?? [],
            'Master' => $rankedService->masterLeague()['entries'] ?? [],
            default => $rankedService->diamondOneEntries(1),
        };

        $total = count($entries);
        $withSummonerId = 0;
        $withPuuid = 0;

        foreach ($entries as $entry) {
            if (is_string($entry['summonerId'] ?? null) && ($entry['summonerId'] ?? '') !== '') {
                $withSummonerId++;
            }
            if (is_string($entry['puuid'] ?? null) && ($entry['puuid'] ?? '') !== '') {
                $withPuuid++;
            }
        }

        $this->line(sprintf('  details: total=%d, summonerId=%d, puuid=%d', $total, $withSummonerId, $withPuuid));
    }
}
