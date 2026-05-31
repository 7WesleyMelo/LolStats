import Link from "next/link";

type QueryValue = string | string[] | undefined;

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
  const configured = process.env.NEXT_PUBLIC_API_BASE_URL;
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
  const minPartidas = asString(query.min_partidas) ?? "20";

  const qs = new URLSearchParams();
  if (patch) qs.set("patch", patch);
  if (posicao) qs.set("posicao", posicao.toUpperCase());
  if (minPartidas) qs.set("min_partidas", minPartidas);

  const suffix = qs.toString() ? `?${qs.toString()}` : "";
  const championSlug = encodeURIComponent(campeao);

  const [overview, itens, runas, matchups] = await Promise.all([
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
  ]);

  return (
    <main className="min-h-screen bg-slate-950 text-slate-100">
      <div className="mx-auto max-w-6xl px-4 py-10 md:px-8">
        <div className="mb-8 flex flex-wrap items-center justify-between gap-4">
          <div>
            <p className="text-sm uppercase tracking-widest text-cyan-300/80">
              Champion Report
            </p>
            <h1 className="text-4xl font-bold">{campeao}</h1>
          </div>
          <Link
            href="/"
            className="rounded-md border border-cyan-300/40 px-3 py-2 text-sm text-cyan-200 hover:bg-cyan-300/10"
          >
            Voltar
          </Link>
        </div>

        <form className="mb-8 grid gap-3 rounded-xl border border-slate-800 bg-slate-900/60 p-4 md:grid-cols-4">
          <input
            type="text"
            name="patch"
            defaultValue={patch ?? ""}
            placeholder="Patch (ex: 16.11)"
            className="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm outline-none focus:border-cyan-400"
          />
          <input
            type="text"
            name="posicao"
            defaultValue={posicao ?? ""}
            placeholder="Posicao (TOP/JUNGLE...)"
            className="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm outline-none focus:border-cyan-400"
          />
          <input
            type="number"
            name="min_partidas"
            min={1}
            defaultValue={minPartidas}
            className="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm outline-none focus:border-cyan-400"
          />
          <button
            type="submit"
            className="rounded-md bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-cyan-400"
          >
            Aplicar filtros
          </button>
        </form>

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
            rows={(itens?.itens ?? []).slice(0, 12).map((row) => [
              String(row.item),
              String(row.partidas),
              `${row.winrate}%`,
              `${row.pickrate}%`,
            ])}
            emptyMessage="Sem dados de itens."
          />

          <TableCard
            title="Runas"
            headers={["Runa", "Partidas", "Winrate", "Pickrate"]}
            rows={(runas?.runas ?? []).slice(0, 12).map((row) => [
              row.runa,
              String(row.partidas),
              `${row.winrate}%`,
              `${row.pickrate}%`,
            ])}
            emptyMessage="Sem dados de runas."
          />

          <TableCard
            title="Matchups"
            headers={["Adversario", "Partidas", "Vitorias", "Winrate"]}
            rows={(matchups?.matchups ?? []).slice(0, 12).map((row) => [
              row.adversario,
              String(row.partidas),
              String(row.vitorias),
              `${row.winrate}%`,
            ])}
            emptyMessage="Sem dados de matchups."
          />
        </div>
      </div>
    </main>
  );
}

function StatCard({ title, value }: { title: string; value: string | number }) {
  return (
    <article className="rounded-xl border border-slate-800 bg-slate-900/80 p-4">
      <p className="text-xs uppercase tracking-wider text-slate-400">{title}</p>
      <p className="mt-2 text-2xl font-bold text-cyan-200">{value}</p>
    </article>
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
  rows: string[][];
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
                <tr key={`${title}-${index}`} className="border-t border-slate-800/70">
                  {row.map((cell, cellIndex) => (
                    <td key={`${title}-${index}-${cellIndex}`} className="px-4 py-2">
                      {cell}
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
