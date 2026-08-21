# Customizing your Sanctum Invoicing instance

> **Standing product rule:** Sanctum Invoicing is the **product**. Your deploy tree is a **thin overlay** for brand, secrets, host wiring, and org-specific ops — not a second place to invent billing features.

This document is for operators who want their own named instance (company brand, Square lanes, Tasks host, internal tools) **above and beyond** stock Sanctum Invoicing.

---

## Why an overlay?

If you fork this repo and keep shipping product features only on your fork, you diverge from upstream forever. The Sanctum pattern (same idea as Sanctum CRM / Sanctum Tasks):

1. **Features invent here** — schema, admin UI, public invoice page, API, SDK, Skin Lab, install shell.
2. **Your overlay repo** (private or public) holds brand defaults, env/secrets docs, host deploy lanes, and any tools that only make sense for your clients or board.
3. **Promote** product changes from this repo into your overlay (rsync/script/checklist), then deploy the overlay.
4. **Do not** point production `REPO` / sync at `sanctumos/sanctum-invoicing` if your overlay carries secrets or org-only tools — keep sync on *your* overlay remote.

```text
sanctumos/sanctum-invoicing          your-org/invoicing-overlay
┌─ product core ──────────────┐     ┌─ your customization ──────────┐
│ billing, API, UI, skins,    │────►│ brand, Square lanes, Tasks    │
│ install shell, AGPL         │promote│ host defaults, org-only tools │
└─────────────────────────────┘     └──────────────┬────────────────┘
                                                   │ your deploy
                                                   ▼
                                          your invoicing hostname
```

---

## What belongs in an overlay (safe customization)

| Customize here | Keep upstream (this repo) |
|---|---|
| App display name / invoice footer brand | Companies, engagements, time, drafts, outbound |
| Default site URL / session cookie name for your hosts | Idempotent schema (`initializeDatabase` / migrate) |
| Square sandbox vs production lane docs + remap helpers | Public invoice + admin product UI |
| Default Tasks API base URL + accounting project id | Skin Lab, HTTP API, SDK, SMCP plugin |
| One-off backfill / client board tools | Install wizard (refuses non-empty DBs) |
| Deploy lane docs (`dev` / `prod` hosts) | Generic config keys (`site_name`, `invoice_brand_*`) |

Stock product already exposes **config** for:

- `site_name` — admin chrome title  
- `invoice_brand_name` / `invoice_brand_url` — public invoice footer  
- Tasks API base + key + per-engagement accounting source  

Use those first. Only add overlay code when config is not enough.

---

## What must stay idempotent

Schema upgrades in this product use `CREATE TABLE IF NOT EXISTS`, `CREATE INDEX IF NOT EXISTS`, and guarded `ADD COLUMN`. Your overlay must **not** introduce drop/recreate migrations that wipe live data. When you promote upstream, the same ensure path must be safe to run twice on production SQLite.

---

## Suggested overlay checklist

1. Clone or mirror this repo into **your** Git remote.
2. Set brand defaults (`SITE_NAME`, session name, health service string, footer brand).
3. Leave Tasks / accounting project defaults **unset or your own** — do not hardcode another org’s board into the product tree you publish.
4. Quarantine org-only scripts under `tools/` and document them as overlay-only.
5. Add a promote script that copies product surfaces from this upstream without clobbering overlay-only paths.
6. Deploy and sync **your** remote; verify on a non-production host before production.

---

## Related

- Product README (architecture summary at the top)
- Install: `public/install.php` (first boot only; refuses existing data)
