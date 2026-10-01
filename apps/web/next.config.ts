import type { NextConfig } from "next";

const config: NextConfig = {
  transpilePackages: ["@livebic/core"],
  // Shareable short links: livebic.com/@handle
  async rewrites() {
    return [{ source: "/@:handle", destination: "/a/:handle" }];
  },
};

export default config;
