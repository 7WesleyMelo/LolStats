<?php

namespace App\Console\Commands;

use App\Models\Summoner;
use App\Services\Riot\RankedService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Throwable;

class BackfillMissingTiersCommand extends Command
{
    protected $signature = 'lol:backfill-missing-tiers {--limit=300} {--max-retries=3}';

    protected $description = 'Preenche tier/rank/LP para summoners com tier nulo sem reseed completo.';

    public function handle(RankedService $rankedService): int
    {
        $limit = max(1, min(5000, (int) $this->option('limit')));
        $maxRetries = max(0, min(10, (int) $this->option('max-retries')));

        $summoners = Summoner::query()
            ->whereNull('tier')
            ->whereNotNull('puuid')
            ->limit($limit)
            ->get();

        if ($summoners->isEmpty()) {
            $this->info('Nenhum summoner com tier nulo para atualizar.');
            return self::SUCCESS;
        }

        $updated = 0;
        $failed = 0;
        $statusErrors = [];
        $noSoloQueue = 0;
        $firstGenericError = null;

        foreach ($summoners as $summoner) {
            $attempt = 0;
            try {
retry:
                $entries = $rankedService->leagueEntriesByPuuid((string) $summoner->puuid);
                $solo = collect($entries)->firstWhere('queueType', 'RANKED_SOLO_5x5');
                if (!is_array($solo)) {
                    $failed++;
                    $noSoloQueue++;
                    continue;
                }

                $summoner->update([
                    'tier' => $solo['tier'] ?? $summoner->tier,
                    'rank' => $solo['rank'] ?? $summoner->rank,
                    'league_points' => (int) ($solo['leaguePoints'] ?? $summoner->league_points),
                    'wins' => (int) ($solo['wins'] ?? $summoner->wins),
                    'losses' => (int) ($solo['losses'] ?? $summoner->losses),
                    'last_seeded_at' => now(),
                ]);

                $updated++;
                usleep(120000);
            } catch (RequestException $e) {
                $status = $e->response?->status() ?? 0;
                if ($status === 429 && $attempt < $maxRetries) {
                    $attempt++;
                    $retryAfter = (int) ($e->response?->header('Retry-After') ?? 1);
                    $sleepSeconds = max(1, $retryAfter);
                    $this->line("429 em {$summoner->puuid}, aguardando {$sleepSeconds}s (tentativa {$attempt}/{$maxRetries})...");
                    sleep($sleepSeconds);
                    goto retry;
                }

                $failed++;
                $statusErrors[$status] = ($statusErrors[$status] ?? 0) + 1;
                continue;
            } catch (Throwable $e) {
                $failed++;
                if ($firstGenericError === null) {
                    $firstGenericError = get_class($e).': '.$e->getMessage();
                }
                continue;
            }
        }

        $this->info("Backfill concluido. Atualizados: {$updated}. Falhas: {$failed}.");
        if ($noSoloQueue > 0) {
            $this->line("Sem RANKED_SOLO_5x5: {$noSoloQueue}");
        }
        if (!empty($statusErrors)) {
            ksort($statusErrors);
            foreach ($statusErrors as $status => $count) {
                $this->line("HTTP {$status}: {$count}");
            }
        }
        if ($firstGenericError !== null) {
            $this->line('Primeiro erro: '.$firstGenericError);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
