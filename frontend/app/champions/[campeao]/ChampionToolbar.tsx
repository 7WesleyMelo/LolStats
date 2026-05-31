"use client";

import { useMemo, useState } from "react";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { ROLE_OPTIONS } from "./roleIcons";

type Props = {
  campeaoAtual: string;
  campeoes: string[];
  posicaoAtual: string;
};

export default function ChampionToolbar({
  campeaoAtual,
  campeoes,
  posicaoAtual,
}: Props) {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState("");

  const filteredChampions = useMemo(() => {
    const normalized = search.trim().toLowerCase();
    if (!normalized) return campeoes.slice(0, 30);
    return campeoes
      .filter((c) => c.toLowerCase().includes(normalized))
      .slice(0, 30);
  }, [campeoes, search]);

  function goToChampion(campeao: string) {
    const params = new URLSearchParams(searchParams.toString());
    router.push(`/champions/${encodeURIComponent(campeao)}?${params.toString()}`);
    setOpen(false);
  }

  function setRole(role: string) {
    const params = new URLSearchParams(searchParams.toString());
    if (role) params.set("posicao", role);
    else params.delete("posicao");
    router.push(`${pathname}?${params.toString()}`);
  }

  return (
    <div className="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-slate-800/90 pb-5">
      <div className="flex flex-wrap items-center gap-3">
        <div className="relative">
          <button
            type="button"
            onClick={() => setOpen((v) => !v)}
            className="inline-flex min-w-44 items-center gap-3 rounded-md border border-slate-700 bg-[#1a1d25] px-3 py-2.5 text-left text-sm font-semibold hover:border-cyan-400/60"
          >
            <span className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-700 text-xs">
              {campeaoAtual.slice(0, 1).toUpperCase()}
            </span>
            <span>{campeaoAtual}</span>
          </button>

          {open ? (
            <div className="absolute left-0 z-40 mt-2 w-64 overflow-hidden rounded-xl border border-slate-700 bg-[#141822] shadow-2xl">
              <div className="p-3">
                <input
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  placeholder="Buscar campeao"
                  className="w-full rounded-md border border-cyan-400 bg-slate-900 px-3 py-2 text-sm outline-none"
                />
              </div>
              <div className="max-h-72 overflow-y-auto border-t border-slate-700">
                <p className="px-3 py-2 text-xs text-slate-400">Todos os Campeoes</p>
                {filteredChampions.map((name) => (
                  <button
                    key={name}
                    type="button"
                    onClick={() => goToChampion(name)}
                    className="flex w-full items-center gap-3 px-3 py-2 text-left text-sm hover:bg-slate-800"
                  >
                    <span className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-700 text-xs">
                      {name.slice(0, 1).toUpperCase()}
                    </span>
                    <span>{name}</span>
                  </button>
                ))}
              </div>
            </div>
          ) : null}
        </div>

        <div className="inline-flex overflow-hidden rounded-md border border-slate-700 bg-[#0f131b]">
          {ROLE_OPTIONS.map((role) => {
            const selected = (posicaoAtual || "").toUpperCase() === role.key;
            return (
              <button
                key={role.label}
                type="button"
                onClick={() => setRole(role.key)}
                className={`flex h-10 w-12 items-center justify-center border-r border-slate-700/90 last:border-r-0 ${
                  selected
                    ? "bg-[#262a35] text-[#3ef2ca]"
                    : "text-slate-200 hover:bg-slate-800"
                }`}
                title={role.label}
                aria-label={role.label}
              >
                {role.icon}
              </button>
            );
          })}
        </div>

        <div className="inline-flex h-10 items-center gap-2 rounded-md border border-slate-700 bg-[#0f131b] px-4 text-sm text-cyan-200">
          <span className="font-semibold text-slate-200">vs</span>
          <span className="text-emerald-300">Todos os Campeoes</span>
        </div>
      </div>
    </div>
  );
}
