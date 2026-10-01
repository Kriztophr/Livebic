export interface Factor {
  key: string;
  label: string;
  points: number;
}

export interface ReleaseView {
  id: string;
  title: string;
  description: string;
  lyrics: string | null;
  access: "public" | "supporters" | "paid";
  priceKobo: number | null;
  edition: { size: number; remaining: number; priceKobo: number } | null;
  publishedAt: string | null;
  artist: { id: string; handle: string; displayName: string } | null;
  authorship: { sha256: string; registryRef: string } | null;
  canListen: boolean;
}

export interface FeedResponse {
  feed: string;
  rulesVersion: number;
  computedAt: string;
  total: number;
  items: { rank: number; release: ReleaseView; why: Factor[] }[];
}

export interface Me {
  user: { id: string; name: string; email: string; role: "fan" | "artist" | "admin"; artistId: string | null };
  artist: null | {
    id: string;
    handle: string;
    displayName: string;
    verification: string;
    supporterCount: number;
    membershipPriceKobo: number | null;
    payout: { method: "bank" | "stablecoin"; destination: string } | null;
    showWalletAddress: boolean;
    walletAddress: string | null;
    shortLink: string;
  };
  balanceKobo: number;
}
