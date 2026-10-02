// Starts the API and the web app in one process tree, for single-container hosts.
import { spawn } from "node:child_process";

const env = { ...process.env };
// On a single-port host the browser reaches the API through the web app's /api-proxy rewrite,
// so the API's public URL is that proxy and the web origin is the same host.
const publicApi = env.PUBLIC_API_URL ?? env.NEXT_PUBLIC_API_URL;
const webUrl = env.WEB_URL ?? (publicApi ? new URL(publicApi).origin : undefined);
const api = spawn("npx", ["tsx", "src/server.ts"], {
  cwd: "apps/api",
  stdio: "inherit",
  env: { ...env, PORT: env.API_PORT ?? "4000", ...(publicApi ? { PUBLIC_API_URL: publicApi } : {}), ...(webUrl ? { WEB_URL: webUrl } : {}) },
});
const web = spawn("npx", ["next", "start", "-p", env.PORT ?? "3000"], { cwd: "apps/web", stdio: "inherit", env });

const stop = () => {
  api.kill();
  web.kill();
};
for (const child of [api, web]) child.on("exit", (code) => { stop(); process.exit(code ?? 1); });
process.on("SIGTERM", stop);
process.on("SIGINT", stop);
