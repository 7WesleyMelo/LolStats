"use client";

import { useState } from "react";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { ROLE_OPTIONS } from "./roleIcons";

type Props = {
  patches: string[];
  positions: string[];
  selectedPatch: string;
  selectedPosicao: string;
  defaultHideLowPickrate: boolean;
  defaultHideLowOccurrence: boolean;
};

export default function FiltersDrawer({
  patches,
  positions,
  selectedPatch,
  selectedPosicao,
  defaultHideLowPickrate,
  defaultHideLowOccurrence,
}: Props) {
  const [open, setOpen] = useState(false);
  const [patch, setPatch] = useState(selectedPatch);
  const [posicao, setPosicao] = useState(selectedPosicao);
  const [hideLowPickrate, setHideLowPickrate] = useState(defaultHideLowPickrate);
  const [hideLowOccurrence, setHideLowOccurrence] = useState(defaultHideLowOccurrence);

  const pathname = usePathname();
  const searchParams = useSearchParams();
  const router = useRouter();

  function applyFilters() {
    const params = new URLSearchParams(searchParams.toString());

    if (patch.trim()) params.set("patch", patch.trim());
    else params.delete("patch");

    if (posicao.trim()) params.set("posicao", posicao.trim().toUpperCase());
    else params.delete("posicao");

    if (hideLowPickrate) params.set("hide_low_pickrate", "1");
    else params.delete("hide_low_pickrate");

    if (hideLowOccurrence) params.set("hide_low_occurrence", "1");
    else params.delete("hide_low_occurrence");

    router.push(`${pathname}?${params.toString()}`);
    setOpen(false);
  }

  function clearFilters() {
    const params = new URLSearchParams(searchParams.toString());
    ["patch", "posicao", "hide_low_pickrate", "hide_low_occurrence"].forEach((k) =>
      params.delete(k),
    );
    router.push(`${pathname}?${params.toString()}`);
    setOpen(false);
  }

  return (
    <>
      <button
        type="button"
        onClick={() => setOpen(true)}
        className="rounded-md border border-slate-700 bg-slate-900/90 px-3 py-2 text-sm font-semibold text-slate-100 hover:border-cyan-400/60"
      >
        Filtros
      </button>

      {open ? (
        <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-[1px]">
          <aside className="absolute top-0 right-0 h-full w-full max-w-sm border-l border-slate-700 bg-[#12151d]">
            <div className="flex items-center justify-between border-b border-slate-700 px-5 py-4">
              <h3 className="text-3xl font-bold tracking-tight text-slate-100">Filtros</h3>
              <button
                type="button"
                onClick={() => setOpen(false)}
                className="rounded-md border border-slate-700 px-3 py-1 text-slate-300 hover:bg-slate-800"
              >
                X
              </button>
            </div>

            <div className="space-y-6 px-5 py-4 text-slate-200">
              <section>
                <h4 className="mb-3 text-3xl font-semibold">Remendos</h4>
                <div className="mb-4 flex items-end gap-1">
                  {patches.map((p, i) => (
                    <button
                      key={p}
                      type="button"
                      onClick={() => setPatch(p)}
                    className={`w-7 rounded-sm transition ${
                      patch === p ? "bg-emerald-400" : "bg-slate-500 hover:bg-slate-400"
                    }`}
                      style={{ height: `${36 + ((i % 4) + 1) * 8}px` }}
                      title={p}
                      aria-label={`Selecionar patch ${p}`}
                    />
                  ))}
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    onClick={() => setPatch("")}
                    className={`rounded-md border px-3 py-2 text-sm ${
                      patch === "" ? "border-emerald-400 text-emerald-300" : "border-slate-700 text-slate-300"
                    }`}
                  >
                    Todos
                  </button>
                  {patch ? (
                    <button
                      type="button"
                      onClick={() => setPatch("")}
                      className="rounded-md border border-slate-700 px-3 py-2 text-sm text-slate-300"
                    >
                      {patch}
                    </button>
                  ) : null}
                </div>
              </section>

              <section className="border-t border-slate-700 pt-4">
                <h4 className="mb-3 text-3xl font-semibold">Classificacoes</h4>
                <div className="space-y-2 text-base text-slate-300">
                  <label className="flex items-center gap-3"><input type="checkbox" className="h-5 w-5 accent-emerald-400" /> Ouro</label>
                  <label className="flex items-center gap-3"><input type="checkbox" className="h-5 w-5 accent-emerald-400" /> Platina</label>
                  <label className="flex items-center gap-3"><input type="checkbox" defaultChecked className="h-5 w-5 accent-emerald-400" /> Esmeralda</label>
                  <label className="flex items-center gap-3"><input type="checkbox" defaultChecked className="h-5 w-5 accent-emerald-400" /> Diamante</label>
                  <label className="flex items-center gap-3"><input type="checkbox" defaultChecked className="h-5 w-5 accent-emerald-400" /> Mestre+</label>
                </div>
              </section>

              <section className="border-t border-slate-700 pt-4">
                <h4 className="mb-3 text-3xl font-semibold">Regioes</h4>
                <p className="text-sm text-slate-400">Filtro por regiao em breve.</p>
              </section>

              <section className="border-t border-slate-700 pt-4">
                <h4 className="mb-3 text-3xl font-semibold">Posicao</h4>
                <div className="inline-flex overflow-hidden rounded-lg border border-slate-700 bg-[#0f131b]">
                  {ROLE_OPTIONS.filter((option) =>
                    option.key === "" || positions.includes(option.key),
                  ).map((option) => {
                    const selected =
                      option.key === ""
                        ? posicao.trim() === ""
                        : posicao.toUpperCase() === option.key;

                    return (
                      <button
                        key={option.key}
                        type="button"
                        onClick={() => setPosicao(option.key === "" ? "" : option.key)}
                        className={`flex h-11 w-12 items-center justify-center border-r border-slate-700/90 transition last:border-r-0 ${
                          selected
                            ? "bg-[#262a35] text-[#3ef2ca]"
                            : "bg-[#0e1219] text-slate-300 hover:bg-slate-800"
                        }`}
                        title={option.label}
                        aria-label={option.label}
                      >
                        {option.icon}
                      </button>
                    );
                  })}
                </div>
              </section>

              <section className="border-t border-slate-700 pt-4">
                <h4 className="mb-3 text-3xl font-semibold">Avancado</h4>
                <label className="mb-3 flex items-center justify-between">
                  <span className="text-xl">Ocultar baixa taxa de selecao</span>
                  <input
                    type="checkbox"
                    checked={hideLowPickrate}
                    onChange={(e) => setHideLowPickrate(e.target.checked)}
                    className="h-6 w-6 accent-emerald-400"
                  />
                </label>
                <label className="flex items-center justify-between">
                  <span className="text-xl">Ocultar baixa ocorrencia</span>
                  <input
                    type="checkbox"
                    checked={hideLowOccurrence}
                    onChange={(e) => setHideLowOccurrence(e.target.checked)}
                    className="h-6 w-6 accent-emerald-400"
                  />
                </label>
              </section>
            </div>

            <div className="absolute right-0 bottom-0 left-0 flex justify-end gap-2 border-t border-slate-700 bg-[#12151d] px-5 py-4">
              <button
                type="button"
                onClick={clearFilters}
                className="rounded-md border border-slate-700 px-3 py-2 text-sm text-slate-200 hover:bg-slate-800"
              >
                Limpar
              </button>
              <button
                type="button"
                onClick={applyFilters}
                className="rounded-md bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-cyan-400"
              >
                Fechar
              </button>
            </div>
          </aside>
        </div>
      ) : null}
    </>
  );
}
