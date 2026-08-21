# Sanctum Invoicing

Professional-services invoicing (retainers + overage / flat-tier) via **Square**, built for the SanctumOS product family. PHP + SQLite, Skin Lab, API + SMCP ready.

## License

Dual license (same as Sanctum CRM / Sanctum Tasks):

- **Software:** [AGPL-3.0](LICENSE-AGPLv3)
- **Docs:** [CC-BY-SA-4.0](LICENSE-CC-BY-SA)

See [LICENSE](LICENSE).

## Product vs DSC overlay

| Lane | Repo | Role |
|------|------|------|
| **Product** | `sanctumos/sanctum-invoicing` (this repo) | Feature home |
| **Overlay / deploy** | `decisionsciencecorp/invoicing` | DSC brand, Square lanes, Tasks wiring, Ada sync |

Production hosts must **not** flip multihost `REPO` to this Sanctum repo. Promote product changes into the DSC overlay, then Ada syncs.

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
- Tasks accounting project id: **unset (0)** until configured — no PSF hardwiring
- Schema: idempotent `initializeDatabase()` / `php tools/migrate.php`

## Requirements

- PHP 8.1+ with `sqlite3`, `curl`, `openssl`, `json`

## Tests

```bash
php tools/phpunit.phar -c phpunit.xml --coverage-text
```

## Related

- Overlay PRD: Tasks Doc **#1158** / `docs/PRD-SANCTUM-INVOICING-HOME.md` on the DSC repo
