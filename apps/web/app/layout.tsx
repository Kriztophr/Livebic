import type { Metadata, Viewport } from "next";
import { Nav } from "../components/Nav";
import { RegisterSW } from "../components/RegisterSW";
import "./globals.css";

export const metadata: Metadata = {
  title: "Livebic",
  description: "Support Nigerian artists directly. Discover who's rising.",
  manifest: "/manifest.webmanifest",
  icons: { icon: "/icon.svg", apple: "/icon.svg" },
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  themeColor: "#0f7b4a",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en-NG">
      <body>
        <Nav />
        <main>{children}</main>
        <RegisterSW />
      </body>
    </html>
  );
}
