import { naira, sha256Hex } from "@livebic/core";
import type { AppContext } from "./context";
import { newId } from "./ids";
import { applyPaymentResult } from "./modules/payments";
import type { Artist, Order, Release, User } from "./store";

const DAY = 86_400_000;

/** Demo data for sandbox mode: a few Lagos artists, fans and a week of activity. */
export async function seed(ctx: AppContext): Promise<void> {
  const { store } = ctx;
  const now = ctx.now().getTime();
  const at = (daysAgo: number) => new Date(now - daysAgo * DAY);

  const mkUser = (name: string, email: string, role: User["role"], daysAgo: number): User => {
    const u: User = {
      id: newId("usr"),
      email,
      name,
      role,
      createdAt: at(daysAgo),
      verified: true,
      walletId: newId("wal"),
      walletAddress: `0x${sha256Hex(email).slice(0, 40)}`,
      artistId: null,
    };
    store.users.set(u.id, u);
    return u;
  };

  mkUser("Livebic Admin", "admin@livebic.test", "admin", 400);

  const artistSpecs = [
    { name: "Tobi Lagos", handle: "tobilagos", age: 300, bio: "Afro-fusion from Surulere." },
    { name: "Ada Ekene", handle: "adaekene", age: 40, bio: "Alté soul, Yaba studio sessions." },
    { name: "Kunle Waves", handle: "kunlewaves", age: 20, bio: "Amapiano x highlife producer." },
    { name: "Zainab Keys", handle: "zainabkeys", age: 200, bio: "Piano covers and originals." },
  ];
  const artists: Artist[] = [];
  for (const s of artistSpecs) {
    const owner = mkUser(s.name, `${s.handle}@livebic.test`, "artist", s.age);
    const artist: Artist = {
      id: newId("art"),
      ownerUserId: owner.id,
      handle: s.handle,
      displayName: s.name,
      bio: s.bio,
      links: [`https://instagram.com/${s.handle}`],
      createdAt: at(s.age),
      verification: "verified",
      verificationRequest: null,
      membershipPriceKobo: naira(1500),
      payout: { method: "bank", destination: "GTBank ****1234" },
      showWalletAddress: false,
    };
    owner.artistId = artist.id;
    store.artists.set(artist.id, artist);
    artists.push(artist);
  }

  const releases: Release[] = [];
  const titles = [
    ["Danfo Driver", "Third Mainland", "Owambe (Live)"],
    ["Yaba Nights", "Soft Life"],
    ["Log Drum Lagos", "Highlife 2.0"],
    ["Keys in the Rain", "Lekki Sunset"],
  ];
  artists.forEach((artist, i) => {
    titles[i]!.forEach((title, j) => {
      const audio = Buffer.from(`demo-audio:${artist.handle}:${title}`);
      const fingerprint = sha256Hex(audio);
      const objectKey = `audio/demo/${fingerprint}`;
      void ctx.partners.storage.put(objectKey, audio, "audio/mpeg");
      const r: Release = {
        id: newId("rel"),
        artistId: artist.id,
        title,
        description: "",
        lyrics: "",
        access: j === 1 ? "paid" : j === 2 ? "supporters" : "public",
        priceKobo: j === 1 ? naira(800) : null,
        splits: [{ payeeId: artist.ownerUserId, role: "creator", bps: 10_000 }],
        edition: j === 0 ? { size: 50, priceKobo: naira(5000), sold: 0 } : null,
        audio: { objectKey, contentType: "audio/mpeg", sha256: fingerprint, registryTx: `0x${sha256Hex(fingerprint)}`, bytes: audio.length },
        status: "published",
        rightsWarrantedAt: at(Math.min(artistSpecs[i]!.age, 10 + j * 3)),
        createdAt: at(Math.min(artistSpecs[i]!.age, 10 + j * 3)),
        publishedAt: at(Math.min(artistSpecs[i]!.age - 1, 10 - j * 3 + i)),
      };
      store.releases.set(r.id, r);
      releases.push(r);
    });
  });

  const fans = Array.from({ length: 24 }, (_, i) => mkUser(`Fan ${i + 1}`, `fan${i + 1}@livebic.test`, "fan", 30 + i * 5));

  let n = 0;
  for (const fan of fans) {
    // Each fan plays a few tracks and supports one or two artists.
    for (let k = 0; k < 4; k++) {
      const r = releases[(n + k * 3) % releases.length]!;
      store.events.push({ id: newId("evt"), type: "play", actorId: fan.id, artistId: r.artistId, itemId: r.id, at: at((n + k) % 10) });
    }
    const targets = n % 3 === 0 ? [releases[n % releases.length]!, releases[(n * 5) % releases.length]!] : [releases[(n * 7) % releases.length]!];
    for (const r of targets) {
      const kind: Order["kind"] = r.access === "paid" ? "unlock" : r.edition ? "drop" : "tip";
      const amountKobo = kind === "unlock" ? r.priceKobo! : kind === "drop" ? r.edition!.priceKobo : naira([500, 1000, 2000][n % 3]!);
      const order: Order = {
        id: newId("ord"),
        fanId: fan.id,
        artistId: r.artistId,
        releaseId: r.id,
        kind,
        amountKobo,
        chargeMinor: amountKobo,
        currency: "NGN",
        status: "pending",
        checkoutRef: newId("chk"),
        createdAt: at(n % 9),
        paidAt: null,
        settlementRef: null,
        editionNumber: null,
        receipt: null,
        membershipEndsAt: null,
      };
      store.orders.set(order.id, order);
      const realNow = ctx.now;
      ctx.now = () => order.createdAt;
      try {
        await applyPaymentResult(ctx, order.checkoutRef, "success");
      } finally {
        ctx.now = realNow;
      }
    }
    n++;
  }
  // Seeded mail is not real mail.
  if ("sentMail" in ctx.partners) (ctx.partners as { sentMail: unknown[] }).sentMail.length = 0;
}
