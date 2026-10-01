import { notFound, permanentRedirect } from "next/navigation";
import { serverApi } from "../../../lib/server";

/** Old DeepSound track links: /track/<audio_id> */
export default async function LegacyTrack({ params }: { params: Promise<{ audioId: string }> }) {
  const { audioId } = await params;
  const track = await serverApi<{ releaseId: string; handle: string }>(`/v1/legacy/tracks/${encodeURIComponent(audioId)}`);
  if (!track) notFound();
  permanentRedirect(`/a/${track.handle}#${track.releaseId}`);
}
