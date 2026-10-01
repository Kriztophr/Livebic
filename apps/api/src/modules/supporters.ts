import type { FastifyInstance } from "fastify";
import { buildSupporterList, supportersToCsv, type ConfirmedSupport } from "@livebic/core";
import { requireUser, type AppContext } from "../context";
import { forbidden } from "../errors";

export function supporterRows(ctx: AppContext, artistId: string) {
  const supports: ConfirmedSupport[] = ctx.store.paidOrders((o) => o.artistId === artistId).map((o) => {
    const fan = ctx.store.users.get(o.fanId);
    return {
      fanId: o.fanId,
      fanName: fan?.name ?? "",
      fanEmail: fan?.email ?? "",
      artistId: o.artistId,
      kind: o.kind,
      amountKobo: o.amountKobo,
      at: o.paidAt ?? o.createdAt,
    };
  });
  return buildSupporterList(artistId, supports);
}

/**
 * Portable supporter list (spec: creators own their audience). Export is deliberately
 * outside any feature flag or account-standing check so it works regardless of platform status.
 */
export async function registerSupporters(app: FastifyInstance, ctx: AppContext) {
  app.get("/v1/artists/me/supporters", async (req) => {
    const user = requireUser(ctx, req, "artist");
    if (!user.artistId) throw forbidden();
    return supporterRows(ctx, user.artistId);
  });

  app.get("/v1/artists/me/supporters.csv", async (req, reply) => {
    const user = requireUser(ctx, req, "artist");
    if (!user.artistId) throw forbidden();
    const artist = ctx.store.artists.get(user.artistId)!;
    const date = ctx.now().toISOString().slice(0, 10);
    return reply
      .header("content-type", "text/csv; charset=utf-8")
      .header("content-disposition", `attachment; filename="${artist.handle}-supporters-${date}.csv"`)
      .send(supportersToCsv(supporterRows(ctx, user.artistId)));
  });
}
