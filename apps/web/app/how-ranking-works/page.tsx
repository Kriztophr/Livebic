export const dynamic = "force-dynamic";

const API_URL = process.env.API_URL ?? process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:4000";

interface Rules {
  version: number;
  effectiveFrom: string;
  refreshMinutes: number;
  feeds: { feed: string; title: string; purpose: string; signals: string[] }[];
  changelog: { version: number; effectiveFrom: string; summary: string; reason: string; announcedAt: string }[];
}

export default async function HowRankingWorks() {
  let rules: Rules | null = null;
  try {
    const res = await fetch(`${API_URL}/v1/ranking/rules`, { cache: "no-store" });
    if (res.ok) rules = (await res.json()) as Rules;
  } catch {}
  if (!rules) return <p className="muted">Ranking rules are unavailable right now.</p>;

  const now = Date.now();
  return (
    <>
      <h1>How ranking works</h1>
      <p className="muted">
        Every feed on Livebic follows the rules below. Scores refresh every {rules.refreshMinutes} minutes. Any change is
        posted here before it takes effect. Rules version {rules.version}.
      </p>
      <h2>The basics</h2>
      <ul>
        <li>Paying support (tips, unlocks, memberships, own-a-piece) counts far more than plays or likes.</li>
        <li>One real supporter counts for more than many anonymous plays.</li>
        <li>You can switch feeds at any time. Nothing is boosted for money paid to Livebic.</li>
      </ul>
      {rules.feeds.map((f) => (
        <section key={f.feed} className="card" id={f.feed}>
          <strong>{f.title}</strong>
          <div className="muted">{f.purpose}</div>
          <ul>
            {f.signals.map((s) => (
              <li key={s}>{s}</li>
            ))}
          </ul>
        </section>
      ))}
      <h2>Changelog</h2>
      {rules.changelog.map((c) => {
        const upcoming = new Date(c.effectiveFrom).getTime() > now;
        return (
          <div key={c.version} className="card">
            <strong>v{c.version}</strong> {upcoming && <span className="badge">Takes effect {new Date(c.effectiveFrom).toDateString()}</span>}
            <div>{c.summary}</div>
            <div className="muted">Why: {c.reason}</div>
          </div>
        );
      })}
    </>
  );
}
