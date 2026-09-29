<?php
declare(strict_types=1);

$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
session_start();

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'findit_campus';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    $pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Your session expired. Go back, refresh the page, and try again.');
    }
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        flash('error', 'Please sign in to continue.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    $statement = db()->prepare('SELECT role FROM users WHERE id = ?');
    $statement->execute([current_user()['id']]);
    if ($statement->fetchColumn() !== 'admin') {
        http_response_code(403);
        exit('Admins only.');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function render_header(string $title): void
{
    $user = current_user();
    $success = flash('success');
    $error = flash('error');
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5f5ef">
        <title><?= e($title) ?> | FindIt Campus</title>
        <link rel="stylesheet" href="assets/style.css">
        <script src="assets/app.js" defer></script>
        <script>window.findItUserId = <?= $user ? (int)$user['id'] : 'null' ?>; window.findItCsrf = '<?= e(csrf_token()) ?>';</script>
    </head>
    <body>
    <header class="site-header">
        <a class="brand" href="index.php"><span class="brand-mark">F</span><span>FindIt <small>Campus</small></span></a>
        <nav class="nav-links" aria-label="Main navigation">
            <a href="index.php">Browse</a>
            <?php if ($user): ?>
                <a href="post-item.php">Post an item</a>
                <?php if ($user['role'] === 'admin'): ?><a href="admin.php">Admin</a><?php endif; ?>
                <span class="nav-user"><?= e($user['name']) ?></span>
                <form method="post" action="logout.php" class="inline-form">
                    <?= csrf_field() ?><button class="nav-button" type="submit">Sign out</button>
                </form>
            <?php else: ?>
                <a href="login.php">Sign in</a>
                <a class="nav-join" href="register.php">Create account</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="page-shell">
        <?php if ($success): ?><div class="notice notice-success"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="notice notice-error"><?= e($error) ?></div><?php endif; ?>
    <?php
}

function render_footer(): void
{
    ?>
    </main>
    <footer class="site-footer"><span>FindIt Campus</span><span>A shared place to bring lost things home.</span></footer>
    </body>
    </html>
    <?php
}
