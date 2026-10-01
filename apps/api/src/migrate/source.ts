import { createConnection } from "mysql2/promise";
import type { DeepSoundRows } from "./deepsound";

/** Tables with no Livebic equivalent yet; counted in the report so nothing disappears silently. */
const NOT_MIGRATED = [
  "albums", "playlists", "comments", "comment_replies", "blog", "events", "events_tickets", "products",
  "orders", "messages", "conversations", "story", "user_ads", "notifications", "reviews", "copyrights",
];

/** Read everything the import needs from a DeepSound MySQL database. Read-only. */
export async function readDeepSound(mysqlUrl: string): Promise<DeepSoundRows> {
  const db = await createConnection({ uri: mysqlUrl, dateStrings: true, supportBigNumbers: true });
  try {
    const q = async <T>(sql: string): Promise<T[]> => (await db.query(sql))[0] as T[];
    const config: Record<string, string> = {};
    for (const r of await q<{ name: string; value: string }>("SELECT name, value FROM config")) config[r.name] = r.value;

    const notMigrated: Record<string, number> = {};
    for (const t of NOT_MIGRATED) {
      try {
        notMigrated[t] = Number((await q<{ n: number }>(`SELECT COUNT(*) AS n FROM \`${t}\``))[0]?.n ?? 0);
      } catch {
        notMigrated[t] = 0;
      }
    }

    return {
      config,
      users: await q(
        "SELECT id, username, email, password, name, about, facebook, twitter, instagram, website, active, admin, verified, artist, registered, time, balance, wallet FROM users",
      ),
      songs: await q("SELECT id, user_id, audio_id, title, description, lyrics, thumbnail, availability, price, audio_location, time FROM songs"),
      followers: await q("SELECT id, follower_id, following_id, time FROM followers"),
      likes: await q("SELECT id, track_id, user_id, time FROM likes WHERE track_id > 0"),
      views: await q("SELECT id, track_id, user_id, fingerprint, time FROM views WHERE track_id > 0"),
      purchases: await q("SELECT id, user_id, track_id, event_id, price, final_price, time FROM purchases"),
      withdrawals: await q("SELECT id, user_id, amount, currency, status, requested, type FROM withdrawal_requests"),
      notMigrated,
    };
  } finally {
    await db.end();
  }
}
