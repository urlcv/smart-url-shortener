# urlcv/smart-url-shortener

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A **2026-style public URL shortener concept tool** for exploring what a modern branded short-link product needs beyond “paste URL → get short URL”.

> **Live demo:** [urlcv.com/tools/smart-url-shortener](https://urlcv.com/tools/smart-url-shortener)  
> This package powers a free tool on **[URLCV](https://urlcv.com)**.

---

## What this is

This is a **UX / product-shape validator** for a next-generation public shortener hosted on a branded domain like `urlcv.com/xxxx`.

It is intentionally **frontend-only** (Alpine.js) and does **not** implement production redirect infrastructure, authentication, or moderation systems. The goal is to make trust, safety, routing, and share surfaces tangible — quickly.

---

## Why shorteners in 2026 need trust & abuse controls

Public short-link domains are high-value targets:

- Attackers use them to **hide final destinations**
- Abuse causes **browser warnings** and **deliverability issues**
- A single spam wave can **damage the brand domain** for everyone

A modern public shortener must actively earn trust with clear previews, reputation tiers, safe defaults, and moderation triggers.

---

## Key features (concept)

- **Branded short URL preview** (`urlcv.com/{slug}`)
- **Readable slug guidance** + auto-generate
- **Preview-first vs direct redirect** behaviour toggle
- **Expiry** and **password gate** concepts
- **Country** and **device routing** concepts
- **UTM helper** inputs (campaign hygiene)
- **QR mode** with browser-side QR generation
- **Trust & safety simulation** with heuristics and badges
- **Routing simulator** (mobile/desktop, UK/US, first-time vs trusted visitor, expired vs active)
- **Modern analytics expectations** (mocked)
- **Product recommendations** output based on the configured link

---

## How it fits URLCV

URLCV publishes free, standalone utilities in `/tools`. This package is one of those tools — designed to be self-contained and easy to evolve into a real product if the concept validates.

Explore all tools at **[urlcv.com/tools](https://urlcv.com/tools)**.

---

## License

MIT — see [LICENSE](LICENSE).

