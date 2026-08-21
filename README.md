# Sanctum Invoicing

Professional-services invoicing (retainers + overage / flat-tier) via **Square**, built for the SanctumOS product family. PHP + SQLite, Skin Lab, API + SMCP ready.

---

## Standing rule — product vs your instance

> **Sanctum Invoicing is the feature brain.**  
> If you need a branded deploy, private secrets, or org-only tools, keep a **thin overlay** of this product — do not invent billing features only on your fork.

| Lane | Where | Role |
|------|--------|------|
| **Product** | This repo (`sanctumos/sanctum-invoicing`) | Schema, billing, admin/public UI, API, skins, install |
| **Your overlay** | Your Git remote + host | Brand, Square lanes, Tasks host defaults, org-only ops, deploy docs |

**Read next:** [docs/CUSTOMIZING.md](docs/CUSTOMIZING.md) — how to customize an instance above and beyond standard Invoicing without forking the product forever.

Promote product changes *into* your overlay, then deploy the overlay. Do not treat a private deploy tree as a second product.

---

## License

Dual license (same as Sanctum CRM / Sanctum Tasks):

- **Software:** [AGPL-3.0](LICENSE-AGPLv3)
- **Docs:** [CC-BY-SA-4.0](LICENSE-CC-BY-SA)

See [LICENSE](LICENSE).

## Quick start

```bash
cd public
php -S 127.0.0.1:8080
# Fresh DB: open http://127.0.0.1:8080/install.php
# Or: http://127.0.0.1:8080/admin/login.php (auto-migrates schema)
```

Install **refuses** non-empty databases (will not reseed live data).

## Defaults (Sanctum)

- App name: **Sanctum Invoicing** (`SITE_NAME` / config `site_name`)
- Session cookie: `sanctum_invoicing_admin`
- Tasks accounting project id: **unset (0)** until configured — no org board hardwiring in product defaults
- Schema: idempotent `initializeDatabase()` / `php tools/migrate.php`

## Requirements

- PHP 8.1+ with `sqlite3`, `curl`, `openssl`, `json`

## Tests

```bash
php tools/phpunit.phar -c phpunit.xml --coverage-text
```
