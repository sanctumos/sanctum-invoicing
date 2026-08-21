<?php
/**
 * Sanctum Invoicing — first-boot install stub.
 *
 * Refuses to run against an existing non-empty database so live
 * production DBs cannot be reseeded by opening /install.php.
 */
declare(strict_types=1);

define('DSC_INVOICING_SKIP_SESSION', true);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

header('Content-Type: text/html; charset=utf-8');

$dbPath = defined('DB_PATH') ? (string) DB_PATH : '';
$exists = $dbPath !== '' && is_file($dbPath) && filesize($dbPath) > 0;
$adminCount = 0;
$companyCount = 0;
$outboundCount = 0;

if ($exists) {
    try {
        $db = getDbConnection();
        initializeDatabase();
        $adminCount = (int) $db->querySingle('SELECT COUNT(*) FROM admin_users');
        $companyCount = (int) $db->querySingle('SELECT COUNT(*) FROM companies');
        $outboundCount = (int) $db->querySingle('SELECT COUNT(*) FROM outbound_invoices');
    } catch (Throwable $e) {
        http_response_code(500);
        echo '<!DOCTYPE html><html><body><h1>Install blocked</h1><p>Could not inspect database.</p></body></html>';
        exit;
    }
}

$nonEmpty = $exists && ($adminCount > 0 || $companyCount > 0 || $outboundCount > 0);

if ($nonEmpty) {
    http_response_code(403);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Install blocked</title></head><body>';
    echo '<h1>Install blocked</h1>';
    echo '<p>This database already has data. Sanctum Invoicing will not re-run install against a live DB.</p>';
    echo '<p><a href="/admin/login.php">Go to admin login</a></p>';
    echo '</body></html>';
    exit;
}

$errors = [];
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = trim((string) ($_POST['company_name'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? 'admin'));
    $password = (string) ($_POST['password'] ?? '');
    if ($company === '') {
        $errors[] = 'Company / brand name is required.';
    }
    if ($username === '') {
        $errors[] = 'Admin username is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($errors === []) {
        putenv('INVOICING_INITIAL_ADMIN_PASSWORD=' . $password);
        $db = getDbConnection();
        initializeDatabase();
        // Replace bootstrap admin if migrate seeded a default row.
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);
        $st = $db->prepare('UPDATE admin_users SET username = :u, password_hash = :h WHERE id = (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1)');
        $st->bindValue(':u', $username, SQLITE3_TEXT);
        $st->bindValue(':h', $hash, SQLITE3_TEXT);
        $st->execute();
        if (function_exists('set_config')) {
            set_config('site_name', $company);
            set_config('invoice_brand_name', $company);
            set_config('installed_at', gmdate('c'));
        }
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install — Sanctum Invoicing</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 32rem; margin: 2rem auto; padding: 0 1rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input { width: 100%; padding: .5rem; box-sizing: border-box; }
        button { margin-top: 1.25rem; padding: .6rem 1rem; }
        .err { color: #b00020; }
    </style>
</head>
<body>
<?php if ($done): ?>
    <h1>Install complete</h1>
    <p>Sanctum Invoicing is ready.</p>
    <p><a href="/admin/login.php">Sign in</a></p>
<?php else: ?>
    <h1>Sanctum Invoicing install</h1>
    <p>First-boot only. Will refuse if the database already has users, companies, or invoices.</p>
    <?php foreach ($errors as $e): ?>
        <p class="err"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>
    <form method="post">
        <label for="company_name">Company / brand name</label>
        <input id="company_name" name="company_name" required value="<?= htmlspecialchars((string) ($_POST['company_name'] ?? 'Sanctum Invoicing'), ENT_QUOTES, 'UTF-8') ?>">
        <label for="username">Admin username</label>
        <input id="username" name="username" required value="<?= htmlspecialchars((string) ($_POST['username'] ?? 'admin'), ENT_QUOTES, 'UTF-8') ?>">
        <label for="password">Admin password</label>
        <input id="password" name="password" type="password" required minlength="8">
        <button type="submit">Install</button>
    </form>
<?php endif; ?>
</body>
</html>
