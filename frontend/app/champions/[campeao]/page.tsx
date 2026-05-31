import Link from "next/link";
import FiltersDrawer from "./FiltersDrawer";
import ChampionToolbar from "./ChampionToolbar";

type QueryValue = string | string[] | undefined;
type PatchApiItem = { versao: string };
type ChampionApiItem = { riot_id?: string; nome?: string };

type OverviewResponse = {
  campeao: string;
  patch: string | null;
  posicao: string | null;
  geral: {
    partidas: number;
    vitorias: number;
    winrate: string;
    pickrate: string;
    kda: string;
  } | null;
  posicao_principal: {
    posicao: string;
    partidas: number;
    vitorias: number;
    winrate: string;
    pickrate: string;
  } | null;
};

type Matchup = {
  adversario: string;
  partidas: number;
  vitorias: number;
  winrate: string;
};

type MatchupsResponse = {
  campeao: string;
  posicao: string | null;
  patch: string | null;
  matchups: Matchup[];
};

type ItemStat = {
  item: number;
  partidas: number;
  vitorias: number;
  winrate: string;
  pickrate: string;
};

type ItemsResponse = {
  campeao: string;
  posicao: string | null;
  patch: string | null;
  itens: ItemStat[];
};

type RuneStat = {
  runa: string;
  partidas: number;
  vitorias: number;
  winrate: string;
  pickrate: string;
};

type RunesResponse = {
  campeao: string;
  posicao: string | null;
  patch: string | null;
  runas: RuneStat[];
};

function asString(value: QueryValue): string | null {
  if (Array.isArray(value)) {
    return value[0] ?? null;
  }
  return value ?? null;
}

function getApiBaseUrl(): string {
  const configured =
    process.env.API_BASE_URL ?? process.env.NEXT_PUBLIC_API_BASE_URL;
  if (configured && configured.length > 0) {
    return configured.replace(/\/$/, "");
  }

  return "http://localhost:8000/api";
}

async function fetchJson<T>(path: string): Promise<T | null> {
  const url = `${getApiBaseUrl()}${path}`;

  try {
    const response = await fetch(url, {
      cache: "no-store",
      headers: { Accept: "application/json" },
    });

    if (!response.ok) {
      return null;
    }

    return (await response.json()) as T;
  } catch {
    return null;
  }
}

export default async function ChampionPage({
  params,
  searchParams,
}: {
  params: Promise<{ campeao: string }>;
  searchParams: Promise<Record<string, QueryValue>>;
}) {
  const { campeao } = await params;
  const query = await searchParams;

  const patch = asString(query.patch);
  const posicao = asString(query.posicao);
  const hideLowPickrate = asString(query.hide_low_pickrate) === "1";
  const hideLowOccurrence = asString(query.hide_low_occurrence) === "1";

  const qs = new URLSearchParams();
  if (patch) qs.set("patch", patch);
  if (posicao) qs.set("posicao", posicao.toUpperCase());

  const suffix = qs.toString() ? `?${qs.toString()}` : "";
  const championSlug = encodeURIComponent(campeao);

  const [overview, itens, runas, matchups, patchesResponse, championsResponse] = await Promise.all([
    fetchJson<OverviewResponse>(`/campeoes/${championSlug}/overview${suffix}`),
    fetchJson<ItemsResponse>(
      `/campeoes/${championSlug}/itens${suffix}${
        suffix ? "&" : "?"
      }sort=pickrate&direction=desc`,
    ),
    fetchJson<RunesResponse>(
      `/campeoes/${championSlug}/runas${suffix}${
        suffix ? "&" : "?"
      }sort=pickrate&direction=desc`,
    ),
    fetchJson<MatchupsResponse>(
      `/campeoes/${championSlug}/matchups${suffix}${
        suffix ? "&" : "?"
      }sort=winrate&direction=desc`,
    ),
    fetchJson<PatchApiItem[]>("/patches"),
    fetchJson<ChampionApiItem[]>("/campeoes"),
  ]);

  const activeRole = (overview?.posicao_principal?.posicao ?? posicao ?? "ALL").toUpperCase();
  const activePatch = overview?.patch ?? patch ?? "latest";
  const patchOptions = Array.from(
    new Set(
      (patchesResponse ?? [])
        .map((p) => (p.versao ?? "").split(".").slice(0, 2).join("."))
        .filter((p) => p.length > 0),
    ),
  );
  const championOptions = (championsResponse ?? [])
    .map((c) => c.riot_id ?? c.nome ?? "")
    .filter((name) => name.length > 0)
    .sort((a, b) => a.localeCompare(b));
  const positionOptions = ["TOP", "JUNGLE", "MIDDLE", "BOTTOM", "UTILITY"];
  const filteredItems = (itens?.itens ?? [])
    .filter((item) => !hideLowPickrate || Number(item.pickrate) >= 1)
    .filter((item) => !hideLowOccurrence || item.partidas >= 5);
  const filteredRunes = (runas?.runas ?? [])
    .filter((runa) => !hideLowPickrate || Number(runa.pickrate) >= 1)
    .filter((runa) => !hideLowOccurrence || runa.partidas >= 5);
  const filteredMatchups = (matchups?.matchups ?? []).filter(
    (m) => !hideLowOccurrence || m.partidas >= 5,
  );
  const highlightItems = filteredItems.slice(0, 12);
  const highlightRunes = filteredRunes.slice(0, 12);
  const highlightMatchups = filteredMatchups.slice(0, 12);

  return (
    <main className="min-h-screen bg-[#040913] text-slate-100">
      <div className="mx-auto max-w-7xl px-4 py-10 md:px-8">
        <div className="relative mb-8 overflow-hidden rounded-2xl border border-cyan-400/20 bg-gradient-to-br from-cyan-900/30 via-slate-900 to-slate-950 p-6 md:p-8">
          <div className="pointer-events-none absolute -top-14 -right-14 h-48 w-48 rounded-full bg-cyan-400/20 blur-3xl" />
          <div className="pointer-events-none absolute -bottom-16 -left-8 h-44 w-44 rounded-full bg-blue-500/20 blur-3xl" />
          <div className="relative flex flex-wrap items-start justify-between gap-5">
            <div>
              <p className="text-xs uppercase tracking-[0.3em] text-cyan-200/70">
                Build Intelligence
              </p>
              <h1 className="mt-2 text-4xl font-black tracking-tight md:text-5xl">
                {campeao}
              </h1>
              <div className="mt-4 flex flex-wrap items-center gap-2">
                <Pill>{activeRole}</Pill>
                <Pill>Patch {activePatch}</Pill>
                <Pill>Dados por patch selecionado</Pill>
              </div>
            </div>
            <div className="flex gap-2">
              <Link
                href="/"
                className="rounded-md border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-slate-200 hover:border-cyan-400/60"
              >
                Home
              </Link>
              <Link
                href={`/champions/${encodeURIComponent(campeao)}`}
                className="rounded-md border border-cyan-300/40 bg-cyan-400/10 px-3 py-2 text-sm text-cyan-100 hover:bg-cyan-300/20"
              >
                Reset filtros
              </Link>
            </div>
          </div>
        </div>

        <div className="flex items-start justify-between gap-3">
          <ChampionToolbar
            campeaoAtual={campeao}
            campeoes={championOptions}
            posicaoAtual={(posicao ?? "").toUpperCase()}
          />
          <FiltersDrawer
            patches={patchOptions}
            positions={positionOptions}
            selectedPatch={patch ?? ""}
            selectedPosicao={posicao ?? ""}
            defaultHideLowPickrate={hideLowPickrate}
            defaultHideLowOccurrence={hideLowOccurrence}
          />
        </div>

        <section className="mb-6 grid gap-4 md:grid-cols-5">
          <StatCard title="Partidas" value={overview?.geral?.partidas ?? 0} />
          <StatCard title="Vitorias" value={overview?.geral?.vitorias ?? 0} />
          <StatCard title="Winrate" value={`${overview?.geral?.winrate ?? "0"}%`} />
          <StatCard
            title="Pickrate"
            value={`${overview?.geral?.pickrate ?? "0"}%`}
          />
          <StatCard title="KDA" value={overview?.geral?.kda ?? "0"} />
        </section>

        <div className="grid gap-6 lg:grid-cols-3">
          <TableCard
            title="Melhores Itens"
            headers={["Item", "Partidas", "Winrate", "Pickrate"]}
            rows={highlightItems.map((row) => ({
              key: String(row.item),
              cells: [
                `#${row.item}`,
                String(row.partidas),
                `${row.winrate}%`,
                `${row.pickrate}%`,
              ],
              trend: Number(row.winrate),
            }))}
            emptyMessage="Sem dados de itens para os filtros selecionados."
          />

          <TableCard
            title="Runas"
            headers={["Runa", "Partidas", "Winrate", "Pickrate"]}
            rows={highlightRunes.map((row) => ({
              key: row.runa,
              cells: [
                row.runa,
                String(row.partidas),
                `${row.winrate}%`,
                `${row.pickrate}%`,
              ],
              trend: Number(row.winrate),
            }))}
            emptyMessage="Sem dados de runas para os filtros selecionados."
          />

          <TableCard
            title="Matchups"
            headers={["Adversario", "Partidas", "Vitorias", "Winrate"]}
            rows={highlightMatchups.map((row) => ({
              key: row.adversario,
              cells: [
                row.adversario,
                String(row.partidas),
                String(row.vitorias),
                `${row.winrate}%`,
              ],
              trend: Number(row.winrate),
            }))}
            emptyMessage="Sem dados de matchups para os filtros selecionados."
          />
        </div>
      </div>
    </main>
  );
}

function StatCard({ title, value }: { title: string; value: string | number }) {
  return (
    <article className="rounded-xl border border-slate-800 bg-gradient-to-br from-slate-900 to-slate-950 p-4">
      <p className="text-xs uppercase tracking-wider text-slate-400">{title}</p>
      <p className="mt-2 text-2xl font-bold text-cyan-100">{value}</p>
    </article>
  );
}

function Pill({ children }: { children: React.ReactNode }) {
  return (
    <span className="rounded-full border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-xs font-semibold text-cyan-100">
      {children}
    </span>
  );
}

function TableCard({
  title,
  headers,
  rows,
  emptyMessage,
}: {
  title: string;
  headers: string[];
  rows: Array<{ key: string; cells: string[]; trend: number }>;
  emptyMessage: string;
}) {
  return (
    <section className="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/80">
      <header className="border-b border-slate-800 px-4 py-3">
        <h2 className="text-base font-semibold text-cyan-100">{title}</h2>
      </header>
      {rows.length === 0 ? (
        <p className="px-4 py-6 text-sm text-slate-400">{emptyMessage}</p>
      ) : (
        <div className="overflow-x-auto">
          <table className="min-w-full text-sm">
            <thead className="bg-slate-950/70 text-left text-slate-400">
              <tr>
                {headers.map((header) => (
                  <th key={header} className="px-4 py-2 font-medium">
                    {header}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody>
              {rows.map((row, index) => (
                <tr key={`${title}-${row.key}-${index}`} className="group border-t border-slate-800/70">
                  {row.cells.map((cell, cellIndex) => (
                    <td key={`${title}-${row.key}-${cellIndex}`} className="px-4 py-2">
                      {cellIndex === row.cells.length - 1 ? (
                        <div className="flex items-center gap-2">
                          <span>{cell}</span>
                          <span className="h-1.5 w-14 overflow-hidden rounded-full bg-slate-700">
                            <span
                              className={`block h-full ${
                                row.trend >= 52
                                  ? "bg-emerald-400"
                                  : row.trend >= 48
                                    ? "bg-amber-400"
                                    : "bg-rose-400"
                              }`}
                              style={{ width: `${Math.max(3, Math.min(100, row.trend))}%` }}
                            />
                          </span>
                        </div>
                      ) : (
                        cell
                      )}
                    </td>
                  ))}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </section>
  );
}
