import { notFound, permanentRedirect } from "next/navigation";
import { serverApi } from "../../lib/server";

/** Old DeepSound profile links: /<username>. Real routes take precedence over this segment. */
export default async function LegacyProfile({ params }: { params: Promise<{ username: string }> }) {
  const { username } = await params;
  if (username.startsWith("@")) {
    permanentRedirect(`/a/${username.slice(1)}`);
  }
  const artist = await serverApi<{ handle: string }>(`/v1/legacy/users/${encodeURIComponent(username)}`);
  if (!artist) notFound();
  permanentRedirect(`/a/${artist.handle}`);
}
