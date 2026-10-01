import { describe, expect, it } from "vitest";
import { generateReceiptKeys, signReceipt, verifyReceipt, type SupportReceipt } from "../src";

const receipt: SupportReceipt = {
  receiptId: "rcp_1",
  fanId: "usr_fan",
  artistId: "art_1",
  releaseId: "rel_1",
  kind: "drop",
  tier: "owner",
  amountKobo: 500_000,
  editionNumber: 3,
  issuedAt: "2026-10-01T00:00:00.000Z",
};

describe("verified receipts", () => {
  it("verifies a signed receipt and rejects tampering", () => {
    const keys = generateReceiptKeys();
    const signed = signReceipt(receipt, keys.privateKeyPem);
    expect(verifyReceipt(signed, keys.publicKeyPem)).toBe(true);
    expect(verifyReceipt({ ...signed, receipt: { ...receipt, editionNumber: 1 } }, keys.publicKeyPem)).toBe(false);
  });
});
