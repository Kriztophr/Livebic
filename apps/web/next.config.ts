import type { NextConfig } from "next";

const config: NextConfig = {
  transpilePackages: ["@livebic/core"],
  // Shareable short links: livebic.com/@handle
  // Old DeepSound URLs keep working after the PHP app is retired.
  async redirects() {
    return [
      { source: "/login", destination: "/signin?mode=signin", permanent: true },
      { source: "/signup", destination: "/signin", permanent: true },
      { source: "/register", destination: "/signin", permanent: true },
      { source: "/discover", destination: "/", permanent: true },
      { source: "/new-releases", destination: "/?feed=newest", permanent: true },
    ];
  },
  async rewrites() {
    return [
      { source: "/@:handle", destination: "/a/:handle" },
      // Single-port hosts: the web app forwards API calls to the API process next to it.
      { source: "/api-proxy/:path*", destination: `${process.env.API_INTERNAL_URL ?? "http://localhost:4000"}/:path*` },
    ];
  },
};

export default config;
