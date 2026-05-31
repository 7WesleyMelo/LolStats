<?php

namespace App\Http\Controllers;

use App\Models\ChampionItemStat;
use App\Models\ChampionMatchup;
use App\Models\ChampionPositionStat;
use App\Models\ChampionRuneStat;
use App\Models\ChampionStat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChampionInsightsController extends Controller
{
    public function matchups(string $campeao, Request $request)
    {
        $query = ChampionMatchup::query()->where('campeao', $campeao);

        [$patch, $posicao] = $this->applyCommonFilters($query, $request);
        $this->applyOrder($query, $request, ['partidas', 'vitorias', 'winrate'], 'partidas');

        $matchups = $query->get(['adversario', 'partidas', 'vitorias', 'winrate']);

        return response()->json([
            'campeao' => $campeao,
            'posicao' => $posicao,
            'patch' => $patch,
            'matchups' => $matchups,
        ]);
    }

    public function itens(string $campeao, Request $request)
    {
        $query = ChampionItemStat::query()->where('campeao', $campeao);

        [$patch, $posicao] = $this->applyCommonFilters($query, $request);
        $this->applyOrder($query, $request, ['partidas', 'vitorias', 'winrate', 'pickrate'], 'partidas');

        $itens = $query->get(['item', 'partidas', 'vitorias', 'winrate', 'pickrate']);

        return response()->json([
            'campeao' => $campeao,
            'posicao' => $posicao,
            'patch' => $patch,
            'itens' => $itens,
        ]);
    }

    public function runas(string $campeao, Request $request)
    {
        $query = ChampionRuneStat::query()->where('campeao', $campeao);

        [$patch, $posicao] = $this->applyCommonFilters($query, $request);
        $this->applyOrder($query, $request, ['partidas', 'vitorias', 'winrate', 'pickrate'], 'partidas');

        $runas = $query->get(['runa', 'partidas', 'vitorias', 'winrate', 'pickrate']);

        return response()->json([
            'campeao' => $campeao,
            'posicao' => $posicao,
            'patch' => $patch,
            'runas' => $runas,
        ]);
    }

    public function overview(string $campeao, Request $request)
    {
        $patch = $request->filled('patch') ? (string) $request->query('patch') : null;
        $posicao = $request->filled('posicao') ? strtoupper((string) $request->query('posicao')) : null;
        $minPartidas = max(1, (int) $request->query('min_partidas', 1));

        $geral = ChampionStat::query()
            ->where('campeao', $campeao)
            ->when($patch, fn (Builder $q) => $q->where('patch', $patch))
            ->orderByDesc('partidas')
            ->first();

        $basePosicoes = ChampionPositionStat::query()
            ->where('campeao', $campeao)
            ->when($patch, fn (Builder $q) => $q->where('patch', $patch))
            ->where('partidas', '>=', $minPartidas);

        $posicaoPrincipal = $posicao
            ? $basePosicoes->clone()->where('posicao', $posicao)->orderByDesc('partidas')->first()
            : $basePosicoes->clone()->orderByDesc('partidas')->first();

        $topItens = ChampionItemStat::query()
            ->where('campeao', $campeao)
            ->when($patch, fn (Builder $q) => $q->where('patch', $patch))
            ->when($posicaoPrincipal?->posicao, fn (Builder $q) => $q->where('posicao', $posicaoPrincipal->posicao))
            ->where('partidas', '>=', $minPartidas)
            ->orderByDesc('pickrate')
            ->orderByDesc('partidas')
            ->limit(10)
            ->get(['item', 'partidas', 'vitorias', 'winrate', 'pickrate']);

        $topRunas = ChampionRuneStat::query()
            ->where('campeao', $campeao)
            ->when($patch, fn (Builder $q) => $q->where('patch', $patch))
            ->when($posicaoPrincipal?->posicao, fn (Builder $q) => $q->where('posicao', $posicaoPrincipal->posicao))
            ->where('partidas', '>=', $minPartidas)
            ->orderByDesc('pickrate')
            ->orderByDesc('partidas')
            ->limit(10)
            ->get(['runa', 'partidas', 'vitorias', 'winrate', 'pickrate']);

        $topMatchups = ChampionMatchup::query()
            ->where('campeao', $campeao)
            ->when($patch, fn (Builder $q) => $q->where('patch', $patch))
            ->when($posicaoPrincipal?->posicao, fn (Builder $q) => $q->where('posicao', $posicaoPrincipal->posicao))
            ->where('partidas', '>=', $minPartidas)
            ->orderByDesc('partidas')
            ->limit(10)
            ->get(['adversario', 'partidas', 'vitorias', 'winrate']);

        return response()->json([
            'campeao' => $campeao,
            'patch' => $patch,
            'posicao' => $posicaoPrincipal?->posicao ?? $posicao,
            'geral' => $geral,
            'posicao_principal' => $posicaoPrincipal,
            'itens' => $topItens,
            'runas' => $topRunas,
            'matchups' => $topMatchups,
        ]);
    }

    private function applyCommonFilters(Builder $query, Request $request): array
    {
        $patch = $request->filled('patch') ? (string) $request->query('patch') : null;
        $posicao = $request->filled('posicao') ? strtoupper((string) $request->query('posicao')) : null;
        $minPartidas = max(1, (int) $request->query('min_partidas', 1));

        if ($patch !== null) {
            $query->where('patch', $patch);
        }
        if ($posicao !== null) {
            $query->where('posicao', $posicao);
        }

        $query->where('partidas', '>=', $minPartidas);

        return [$patch, $posicao];
    }

    private function applyOrder(Builder $query, Request $request, array $allowedSorts, string $defaultSort): void
    {
        $sort = (string) $request->query('sort', $defaultSort);
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = $defaultSort;
        }

        $direction = strtolower((string) $request->query('direction', 'desc'));
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sort, $direction)->orderByDesc('partidas');
    }
}
