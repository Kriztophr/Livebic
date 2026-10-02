import { readFileSync, readdirSync, statSync } from "node:fs";
import { join } from "node:path";
import { describe, expect, it } from "vitest";
import { findForbiddenTerms } from "@livebic/core/copy";

/** Fan-facing screens must not use crypto vocabulary (principle 1). Studio is creator-facing and exempt. */
const FAN_DIRS = ["app", "components"];
const CREATOR_ONLY = [join("app", "studio")]; // includes studio/video

function files(dir: string): string[] {
  return readdirSync(dir).flatMap((f) => {
    const p = join(dir, f);
    if (statSync(p).isDirectory()) return CREATOR_ONLY.some((c) => p.endsWith(c)) ? [] : files(p);
    return p.endsWith(".tsx") ? [p] : [];
  });
}

/** Visible text: JSX text nodes and quoted strings inside JSX expressions. */
function visibleText(src: string): string[] {
  const jsxText = [...src.matchAll(/>([^<>{}]+)</g)].map((m) => m[1]!.trim()).filter(Boolean);
  const strings = [...src.matchAll(/"([^"\n]*[A-Za-z][^"\n]* [^"\n]*)"/g)].map((m) => m[1]!);
  return [...jsxText, ...strings];
}

describe("fan-facing copy", () => {
  const root = join(__dirname, "..");
  for (const file of FAN_DIRS.flatMap((d) => files(join(root, d)))) {
    it(`${file.slice(root.length + 1)} has no crypto vocabulary`, () => {
      const hits = visibleText(readFileSync(file, "utf8")).flatMap((t) => findForbiddenTerms(t).map((term) => `${term}: "${t}"`));
      expect(hits).toEqual([]);
    });
  }
});
