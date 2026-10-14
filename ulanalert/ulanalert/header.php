<!DOCTYPE html><html><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= APP ?></title><link rel="stylesheet" href="style.css"></head><body>
<nav><a href="index.php"><b><?= APP ?></b></a>
<?php foreach(NAV as $l=>$u): ?><a href="<?= $u ?>"><?= $l ?></a><?php endforeach; ?>
<span class="sp"></span>
<?php if(!empty($_SESSION['user'])): ?><span><?= e($_SESSION['user']['name']) ?></span><a href="logout.php">Logout</a>
<?php else: ?><a href="login.php">Login</a><a href="register.php">Register</a><?php endif; ?></nav><main>
