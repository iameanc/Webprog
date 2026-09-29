<?php require 'db.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $s=$pdo->prepare('SELECT * FROM users WHERE email=?'); $s->execute([$_POST['email']]); $u=$s->fetch();
  if($u && password_verify($_POST['password'],$u['password'])){
    session_regenerate_id(true);
    $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']];
    header('Location: index.php'); exit; }
  $err='Invalid email or password.'; }
require 'header.php'; ?>
<h2>Login</h2><?php if($err) echo "<p class='err'>$err</p>"; ?>
<form method="post" class="card"><input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required><button>Login</button></form></main></body></html>
