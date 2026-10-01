import { createHash } from "node:crypto";
import { mkdir, readFile, rename, stat, writeFile } from "node:fs/promises";
import { join, resolve, sep } from "node:path";
import { parseArgs } from "node:util";
import type { BundleMedia } from "./bundle";
import { mapDeepSound, reportMarkdown, settlementCsv } from "./deepsound";
import { readDeepSound } from "./source";

const USAGE = `Usage: npm run migrate:deepsound -w @livebic/api -- \\
  --mysql mysql://user:pass@host:3306/deepsound \\
  --media <path to the old Script/ folder, or the base URL its uploads are served from> \\
  --out ./deepsound-export \\
  [--ngn-per-unit 1550]   required when the old site priced in a currency other than NGN

The MySQL URL can also come from DEEPSOUND_MYSQL_URL. Nothing is written to the old database.`;

const MAX_MEDIA_BYTES = 200 * 1024 * 1024;

async function fetchMedia(base: string, legacyPath: string): Promise<Buffer> {
  if (/^https?:\/\//.test(legacyPath)) {
    // Tracks imported from other sites store a full URL.
    const res = await fetch(legacyPath);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return Buffer.from(await res.arrayBuffer());
  }
  if (/^https?:\/\//.test(base)) {
    const url = `${base.replace(/\/$/, "")}/${legacyPath.split("/").map(encodeURIComponent).join("/")}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return Buffer.from(await res.arrayBuffer());
  }
  const root = resolve(base);
  const file = resolve(root, legacyPath);
  if (!file.startsWith(root + sep)) throw new Error("path escapes the media folder");
  const info = await stat(file);
  if (info.size > MAX_MEDIA_BYTES) throw new Error("file too large");
  return readFile(file);
}

async function main() {
  const { values } = parseArgs({
    options: {
      mysql: { type: "string" },
      media: { type: "string" },
      out: { type: "string" },
      "ngn-per-unit": { type: "string" },
      help: { type: "boolean" },
    },
  });
  const mysqlUrl = values.mysql ?? process.env.DEEPSOUND_MYSQL_URL;
  if (values.help || !mysqlUrl || !values.media || !values.out) {
    console.log(USAGE);
    process.exit(values.help ? 0 : 1);
  }
  const out = resolve(values.out);
  const mediaDir = join(out, "media");
  await mkdir(mediaDir, { recursive: true });

  console.log("Reading DeepSound database…");
  const rows = await readDeepSound(mysqlUrl);
  const { bundle, report } = mapDeepSound(rows, { ngnPerUnit: values["ngn-per-unit"] ? Number(values["ngn-per-unit"]) : undefined });

  const files: BundleMedia[] = bundle.releases.flatMap((r) => [r.audio, r.cover].filter((m): m is BundleMedia => m !== null));
  console.log(`Copying ${files.length} media files…`);
  const missing: string[] = [];
  let copied = 0;
  let next = 0;
  const worker = async () => {
    while (next < files.length) {
      const m = files[next++]!;
      try {
        const body = await fetchMedia(values.media!, m.legacyPath);
        const sha256 = createHash("sha256").update(body).digest("hex");
        const target = join(mediaDir, sha256);
        await writeFile(`${target}.tmp`, body);
        await rename(`${target}.tmp`, target);
        m.sha256 = sha256;
        m.bytes = body.length;
        copied++;
      } catch (e) {
        missing.push(`${m.legacyPath} (${(e as Error).message})`);
      }
    }
  };
  await Promise.all(Array.from({ length: 8 }, worker));

  await writeFile(join(out, "bundle.json"), JSON.stringify(bundle));
  await writeFile(join(out, "report.md"), reportMarkdown(report, { mediaCopied: copied, mediaMissing: missing }));
  await writeFile(join(out, "settlement.csv"), settlementCsv(report));
  console.log(`Done. ${bundle.users.length} users, ${bundle.artists.length} artists, ${bundle.releases.length} releases, ${bundle.orders.length} purchases.`);
  console.log(`Report: ${join(out, "report.md")}\nSettlement list: ${join(out, "settlement.csv")}`);
  console.log(`Load it with: LIVEBIC_IMPORT_BUNDLE=${out} npm run dev:api`);
}

main().catch((e) => {
  console.error(e instanceof Error ? e.message : e);
  process.exit(1);
});
