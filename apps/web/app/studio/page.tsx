"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { formatNaira, naira } from "@livebic/core/money";
import { api, API_URL, getToken } from "../../lib/api";
import { useMe } from "../../lib/useMe";

interface Supporter {
  fanId: string;
  name: string;
  tier: string;
  totalSupportKobo: number;
  supportCount: number;
  lastSupportedAt: string;
}

export default function Studio() {
  const { me, loading, reload } = useMe();
  const [supporters, setSupporters] = useState<Supporter[]>([]);
  const [msg, setMsg] = useState<string | null>(null);

  useEffect(() => {
    if (me?.artist) api<Supporter[]>("/v1/artists/me/supporters").then(setSupporters).catch(() => {});
  }, [me?.artist?.id]);

  if (loading) return <p className="muted">Loading…</p>;
  if (!me?.artist) return <p>Studio is for artists. <Link href="/signin">Create an artist account</Link>.</p>;
  const artist = me.artist;

  async function downloadCsv() {
    // Fetch with the session header, then hand the file to the browser.
    const res = await fetch(`${API_URL}/v1/artists/me/supporters.csv`, { headers: { authorization: `Bearer ${getToken()}` } });
    const blob = await res.blob();
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = `${artist.handle}-supporters.csv`;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  return (
    <>
      <h1>Studio</h1>
      <p className="muted">
        Your page: <Link href={`/a/${artist.handle}`}>{artist.shortLink}</Link>
      </p>
      {msg && <p className="notice">{msg}</p>}

      {artist.verification !== "verified" && <Verification status={artist.verification} onDone={reload} />}

      <p><Link href="/studio/video" className="btn secondary">Video studio →</Link></p>

      <h2>Earnings</h2>
      <Earnings balanceKobo={me.balanceKobo} payout={artist.payout} onDone={(m) => { setMsg(m); reload(); }} />

      <h2>New release</h2>
      <NewRelease onDone={(m) => setMsg(m)} />

      <h2>Your supporters ({supporters.length})</h2>
      <p className="muted">This list is yours. Download it any time.</p>
      <button className="btn secondary" onClick={downloadCsv}>Download CSV</button>
      <div className="table-wrap">
        <table style={{ marginTop: 12 }}>
          <thead>
            <tr><th>Name</th><th>Tier</th><th className="num">Total</th><th className="num">Times</th></tr>
          </thead>
          <tbody>
            {supporters.map((s) => (
              <tr key={s.fanId}>
                <td>{s.name}</td><td>{s.tier}</td><td className="num">{formatNaira(s.totalSupportKobo)}</td><td className="num">{s.supportCount}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </>
  );
}

function Verification({ status, onDone }: { status: string; onDone: () => void }) {
  const [idRef, setIdRef] = useState("");
  const [social, setSocial] = useState("");
  const [error, setError] = useState<string | null>(null);
  if (status === "pending") return <p className="card">Verification is being reviewed. You can publish now; payouts unlock once you&apos;re verified.</p>;
  return (
    <form
      className="card"
      onSubmit={async (e) => {
        e.preventDefault();
        try {
          await api("/v1/artists/me/verification", { method: "POST", json: { idDocumentRef: idRef, socialProofUrls: [social] } });
          onDone();
        } catch (err) {
          setError((err as Error).message);
        }
      }}
    >
      <strong>Get verified</strong>
      <div className="muted">Needed before your first payout.</div>
      <label htmlFor="nin">NIN or passport number</label>
      <input id="nin" required value={idRef} onChange={(e) => setIdRef(e.target.value)} />
      <label htmlFor="soc">A social profile that proves it&apos;s you</label>
      <input id="soc" type="url" required placeholder="https://instagram.com/…" value={social} onChange={(e) => setSocial(e.target.value)} />
      {error && <p className="error">{error}</p>}
      <p><button className="btn">Submit</button></p>
    </form>
  );
}

function Earnings({ balanceKobo, payout, onDone }: { balanceKobo: number; payout: { method: string; destination: string } | null; onDone: (m: string) => void }) {
  const [method, setMethod] = useState(payout?.method ?? "bank");
  const [destination, setDestination] = useState(payout?.destination ?? "");
  const [amount, setAmount] = useState("");
  const [error, setError] = useState<string | null>(null);
  return (
    <div className="card">
      <div style={{ fontSize: "1.6rem", fontWeight: 700 }}>{formatNaira(balanceKobo)}</div>
      <div className="muted">Available now. Sales are available within minutes.</div>
      <form
        onSubmit={async (e) => {
          e.preventDefault();
          setError(null);
          try {
            await api("/v1/artists/me", { method: "PATCH", json: { payout: { method, destination } } });
            const p = await api<{ status: string }>("/v1/payouts", { method: "POST", idempotent: true, json: { amountKobo: naira(Number(amount)) } });
            onDone(p.status === "pending_approval" ? "Payout requested. Large payouts get a quick manual check first." : "Payout on its way.");
          } catch (err) {
            setError((err as Error).message);
          }
        }}
      >
        <label htmlFor="method">Get paid in</label>
        <select id="method" value={method} onChange={(e) => setMethod(e.target.value)}>
          <option value="bank">Naira to my bank account</option>
          <option value="stablecoin">US dollars (USDC) to my account</option>
        </select>
        <label htmlFor="dest">{method === "bank" ? "Bank and account number" : "Payout address"}</label>
        <input id="dest" required value={destination} onChange={(e) => setDestination(e.target.value)} />
        <label htmlFor="amt">Amount (₦)</label>
        <input id="amt" type="number" min={1000} step={1} required value={amount} onChange={(e) => setAmount(e.target.value)} />
        {error && <p className="error">{error}</p>}
        <p><button className="btn">Request payout</button></p>
      </form>
    </div>
  );
}

function NewRelease({ onDone }: { onDone: (m: string) => void }) {
  const [title, setTitle] = useState("");
  const [access, setAccess] = useState("public");
  const [price, setPrice] = useState("");
  const [editionSize, setEditionSize] = useState("");
  const [editionPrice, setEditionPrice] = useState("");
  const [file, setFile] = useState<File | null>(null);
  const [warrant, setWarrant] = useState(false);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  return (
    <form
      className="card"
      onSubmit={async (e) => {
        e.preventDefault();
        if (!file) return;
        setBusy(true);
        setError(null);
        try {
          const rel = await api<{ id: string }>("/v1/releases", {
            method: "POST",
            json: {
              title,
              access,
              priceKobo: access === "paid" ? naira(Number(price)) : null,
              edition: editionSize ? { size: Number(editionSize), priceKobo: naira(Number(editionPrice)) } : null,
              rightsWarranty: warrant,
            },
          });
          await api(`/v1/releases/${rel.id}/audio`, { method: "PUT", body: file, headers: { "content-type": file.type || "audio/mpeg" } });
          await api(`/v1/releases/${rel.id}/publish`, { method: "POST" });
          onDone(`"${title}" is live. Its authorship record has been saved.`);
          setTitle("");
          setFile(null);
        } catch (err) {
          setError((err as Error).message);
        } finally {
          setBusy(false);
        }
      }}
    >
      <label htmlFor="title">Title</label>
      <input id="title" required value={title} onChange={(e) => setTitle(e.target.value)} />
      <label htmlFor="file">Audio (MP3 or WAV)</label>
      <input id="file" type="file" accept="audio/mpeg,audio/wav" required onChange={(e) => setFile(e.target.files?.[0] ?? null)} />
      <label htmlFor="access">Who can listen</label>
      <select id="access" value={access} onChange={(e) => setAccess(e.target.value)}>
        <option value="public">Everyone</option>
        <option value="supporters">Supporters only</option>
        <option value="paid">Paid unlock</option>
      </select>
      {access === "paid" && (
        <>
          <label htmlFor="price">Unlock price (₦)</label>
          <input id="price" type="number" min={100} required value={price} onChange={(e) => setPrice(e.target.value)} />
        </>
      )}
      <label htmlFor="ed">Own-a-piece editions (optional)</label>
      <div className="row">
        <input id="ed" type="number" min={1} placeholder="How many" value={editionSize} onChange={(e) => setEditionSize(e.target.value)} style={{ flex: 1 }} />
        <input type="number" min={100} placeholder="Price ₦" value={editionPrice} required={!!editionSize} onChange={(e) => setEditionPrice(e.target.value)} style={{ flex: 1 }} />
      </div>
      <label style={{ display: "flex", gap: 8, alignItems: "flex-start" }}>
        <input type="checkbox" checked={warrant} required onChange={(e) => setWarrant(e.target.checked)} style={{ width: "auto", marginTop: 4 }} />
        I own or have licensed everything in this release, including samples and beats.
      </label>
      {error && <p className="error">{error}</p>}
      <p><button className="btn" disabled={busy}>{busy ? "Publishing…" : "Publish"}</button></p>
    </form>
  );
}
