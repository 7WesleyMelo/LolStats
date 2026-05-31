import { redirect } from "next/navigation";

export default async function ChampionsPage({
  searchParams,
}: {
  searchParams: Promise<{ campeao?: string }>;
}) {
  const { campeao } = await searchParams;
  const normalized = (campeao ?? "").trim();

  if (normalized.length > 0) {
    redirect(`/champions/${encodeURIComponent(normalized)}`);
  }

  return (
    <main className="flex min-h-screen items-center justify-center bg-slate-950 px-4 text-slate-200">
      <p className="text-sm">Informe um campeao na URL: /champions?campeao=Aatrox</p>
    </main>
  );
}
