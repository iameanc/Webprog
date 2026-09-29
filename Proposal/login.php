<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
if (current_user()) {
    redirect('index.php');
}
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $statement = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $statement->execute([$email]);
    $user = $statement->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        redirect('index.php');
    }
    $error = 'Email or password was not recognized.';
}
render_header('Sign in');
?>
<section class="auth-layout"><div class="auth-aside"><p class="eyebrow">FINDIT CAMPUS</p><h1>One board.<br>A little less<br><em>lost.</em></h1><p>Sign in to share a discovery or start a claim for a found item.</p></div>
<div class="form-panel"><p class="eyebrow">WELCOME BACK</p><h2>Sign in</h2>
<?php if ($error): ?><div class="notice notice-error"><?= e($error) ?></div><?php endif; ?>
<form class="stack-form" method="post" action="login.php">
    <?= csrf_field() ?>
    <label>Campus email<input name="email" type="email" autocomplete="email" value="<?= e($email) ?>" required></label>
    <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
    <button class="button button-wide" type="submit">Sign in</button>
</form><p class="form-footnote">Need an account? <a href="register.php">Create one</a></p></div></section>
<?php render_footer(); ?>
