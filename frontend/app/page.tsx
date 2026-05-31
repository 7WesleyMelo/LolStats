import Link from "next/link";

export default function Home() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-slate-950 px-4 text-slate-100">
      <main className="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900/80 p-8">
        <p className="text-xs uppercase tracking-[0.2em] text-cyan-300">LolStats</p>
        <h1 className="mt-2 text-3xl font-bold">Pagina de Campeao</h1>
        <p className="mt-3 text-sm text-slate-400">
          Digite um campeao para abrir os insights de overview, itens, runas e
          matchups.
        </p>

        <form action="/champions" className="mt-8 flex flex-col gap-3 sm:flex-row">
          <input
            type="text"
            name="campeao"
            placeholder="Ex: Aatrox"
            className="flex-1 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm outline-none focus:border-cyan-400"
            required
          />
          <Link
            className="inline-flex items-center justify-center rounded-md bg-cyan-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-cyan-400"
            href="/champions/Aatrox"
          >
            Abrir Aatrox
          </Link>
        </form>
        <p className="mt-4 text-xs text-slate-500">
          Rota direta: <code>/champions/[campeao]</code>
        </p>
      </main>
    </div>
  );
}
