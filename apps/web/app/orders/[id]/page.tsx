"use client";

import Link from "next/link";
import { useParams } from "next/navigation";
import { useEffect, useState } from "react";
import { formatNaira } from "@livebic/core/money";
import { api } from "../../../lib/api";

interface Order {
  id: string;
  kind: string;
  status: string;
  amountKobo: number;
  editionNumber: number | null;
  membershipEndsAt: string | null;
  artistId: string;
  receipt: null | { receiptId: string; issuedAt: string };
}

const KIND = { tip: "Tip", unlock: "Unlock", membership: "Membership", drop: "Own a piece" } as Record<string, string>;

export default function OrderPage() {
  const { id } = useParams<{ id: string }>();
  const [order, setOrder] = useState<Order | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let stop = false;
    let tries = 0;
    const poll = async () => {
      try {
        const o = await api<Order>(`/v1/orders/${id}`);
        if (stop) return;
        setOrder(o);
        if (o.status === "pending" && ++tries < 20) setTimeout(poll, 1500);
      } catch (e) {
        setError((e as Error).message);
      }
    };
    void poll();
    return () => {
      stop = true;
    };
  }, [id]);

  if (error) return <p className="error">{error}</p>;
  if (!order) return <p className="muted">Checking your payment…</p>;
  return (
    <>
      <h1>
        {order.status === "paid" ? "Thank you!" : order.status === "pending" ? "Confirming payment…" : order.status === "refund_required" ? "Sold out — refund on the way" : "Payment didn't go through"}
      </h1>
      <div className="card">
        <p>
          {KIND[order.kind]} · {formatNaira(order.amountKobo)}
        </p>
        {order.editionNumber && <p>You own edition #{order.editionNumber}.</p>}
        {order.membershipEndsAt && <p className="muted">Member until {new Date(order.membershipEndsAt).toDateString()}.</p>}
        {order.receipt && (
          <p className="muted">
            Receipt {order.receipt.receiptId} · emailed to you. This receipt is permanently recorded and can be verified by anyone.
          </p>
        )}
        {order.status === "failed" && <p className="muted">You weren&apos;t charged. Try another payment method such as bank transfer or USSD.</p>}
      </div>
      <Link href="/" className="btn secondary">Back to feeds</Link>
    </>
  );
}
