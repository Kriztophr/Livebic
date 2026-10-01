import { FeedView } from "../components/FeedView";

export default function Home() {
  return (
    <>
      <h1>Find artists before everyone else</h1>
      <p className="muted">Pay artists directly. Every feed&apos;s rules are public — tap &ldquo;Why this?&rdquo; on anything.</p>
      <FeedView />
    </>
  );
}
