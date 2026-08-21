# Deploy lanes (example pattern)

Sanctum Invoicing itself does not prescribe a host. Operators typically keep **dev** and **prod** lanes on *their* overlay remote:

| Lane | Git branch | Square | Notes |
|------|------------|--------|-------|
| **Dev** | `dev` | Sandbox | Verify schema + UI before production |
| **Prod** | `main` | Production | Sync only after DEV proof |

## Schema

App entrypoints run idempotent `initializeDatabase()` (`CREATE IF NOT EXISTS` + guarded `ADD COLUMN`). Optional: `php tools/migrate.php` against the **vhost** DB path.

See [CUSTOMIZING.md](./CUSTOMIZING.md) for the overlay / promote pattern.
