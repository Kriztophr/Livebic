import { createPrivateKey, createPublicKey, generateKeyPairSync, sign, verify, type KeyObject } from "node:crypto";
import { sha256Hex } from "./fingerprint";

/**
 * Ownership model A ("verified receipt"): a signed record that fan X supported
 * release Y at tier Z. Only the receipt hash goes to the chain registry; the
 * receipt itself contains no personal data beyond opaque ids.
 */
export interface SupportReceipt {
  receiptId: string;
  fanId: string;
  artistId: string;
  releaseId: string | null;
  kind: string;
  tier: string;
  amountKobo: number;
  editionNumber: number | null;
  issuedAt: string;
}

export interface SignedReceipt {
  receipt: SupportReceipt;
  hash: string;
  signature: string;
}

function canonical(receipt: SupportReceipt): string {
  const keys = Object.keys(receipt).sort() as (keyof SupportReceipt)[];
  return JSON.stringify(keys.map((k) => [k, receipt[k]]));
}

export function generateReceiptKeys(): { privateKeyPem: string; publicKeyPem: string } {
  const { privateKey, publicKey } = generateKeyPairSync("ed25519");
  return {
    privateKeyPem: privateKey.export({ type: "pkcs8", format: "pem" }).toString(),
    publicKeyPem: publicKey.export({ type: "spki", format: "pem" }).toString(),
  };
}

export function signReceipt(receipt: SupportReceipt, privateKeyPem: string | KeyObject): SignedReceipt {
  const key = typeof privateKeyPem === "string" ? createPrivateKey(privateKeyPem) : privateKeyPem;
  const body = canonical(receipt);
  return {
    receipt,
    hash: sha256Hex(body),
    signature: sign(null, Buffer.from(body), key).toString("base64"),
  };
}

export function verifyReceipt(signed: SignedReceipt, publicKeyPem: string | KeyObject): boolean {
  const key = typeof publicKeyPem === "string" ? createPublicKey(publicKeyPem) : publicKeyPem;
  const body = canonical(signed.receipt);
  if (sha256Hex(body) !== signed.hash) return false;
  return verify(null, Buffer.from(body), key, Buffer.from(signed.signature, "base64"));
}
