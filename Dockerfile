# One image that runs both the API (port 4000) and the web app (port 3000).
# Sandbox mode: in-memory data with demo artists, mock payment partners. Data resets on restart.
FROM node:22-slim AS build
WORKDIR /app
COPY package.json package-lock.json ./
COPY packages/core/package.json packages/core/
COPY apps/api/package.json apps/api/
COPY apps/web/package.json apps/web/
RUN npm ci
COPY . .
ENV NEXT_TELEMETRY_DISABLED=1
# The web app bakes the public API URL in at build time.
ARG NEXT_PUBLIC_API_URL=http://localhost:4000
ENV NEXT_PUBLIC_API_URL=$NEXT_PUBLIC_API_URL
RUN npm run build -w @livebic/web

FROM node:22-slim
WORKDIR /app
ARG NEXT_PUBLIC_API_URL=http://localhost:4000
ENV NODE_ENV=production NEXT_TELEMETRY_DISABLED=1 NEXT_PUBLIC_API_URL=$NEXT_PUBLIC_API_URL
COPY --from=build /app .
EXPOSE 3000 4000
CMD ["node", "scripts/start.mjs"]
