<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
if (current_user()) {
    redirect('index.php');
}
$error = '';
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if ($name === '' || mb_strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter your name and a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Your password must be at least 8 characters.';
    } else {
        try {
            $statement = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)db()->lastInsertId(), 'name' => $name, 'email' => $email, 'role' => 'user'];
            flash('success', 'Your account is ready. You can now post and claim items.');
            redirect('index.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'An account already exists for that email.';
            } else {
                throw $exception;
            }
        }
    }
}
render_header('Create account');
?>
<section class="auth-layout"><div class="auth-aside"><p class="eyebrow">JOIN YOUR CAMPUS</p><h1>Good things<br>find their way<br><em>back.</em></h1><p>Make a free account to share listings and securely claim something that belongs to you.</p></div>
<div class="form-panel"><p class="eyebrow">NEW ACCOUNT</p><h2>Create account</h2>
<?php if ($error): ?><div class="notice notice-error"><?= e($error) ?></div><?php endif; ?>
<form class="stack-form" method="post" action="register.php">
    <?= csrf_field() ?>
    <label>Full name<input name="name" type="text" maxlength="120" autocomplete="name" value="<?= e($name) ?>" required></label>
    <label>Campus email<input name="email" type="email" maxlength="190" autocomplete="email" value="<?= e($email) ?>" required></label>
    <label>Password<input name="password" type="password" minlength="8" autocomplete="new-password" required><small>Use at least 8 characters.</small></label>
    <button class="button button-wide" type="submit">Create account</button>
</form><p class="form-footnote">Already registered? <a href="login.php">Sign in</a></p></div></section>
<?php render_footer(); ?>
