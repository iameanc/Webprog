<?php require 'db.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $role = (defined('ROLES') && in_array($_POST['role']??'',ROLES)) ? $_POST['role'] : 'user';
  if($role==='admin') $role='user'; // admins are created manually in the DB
  if(strlen($_POST['password'])<6) $err='Password must be at least 6 characters.';
  else try{
    $pdo->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)')
        ->execute([$_POST['name'],$_POST['email'],password_hash($_POST['password'],PASSWORD_DEFAULT),$role]);
    header('Location: login.php'); exit;
  }catch(PDOException $x){ $err='Email already registered.'; } }
require 'header.php'; ?>
<h2>Register</h2><?php if($err) echo "<p class='err'>$err</p>"; ?>
<form method="post" class="card"><input name="name" placeholder="Full name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<?php if(defined('ROLES')): ?><select name="role"><?php foreach(ROLES as $r) echo "<option>$r</option>"; ?></select><?php endif; ?>
<button>Create account</button></form></main></body></html>
