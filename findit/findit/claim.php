<?php require 'db.php'; require_login();
$id=(int)($_GET['id']??$_POST['id']??0);
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare('INSERT INTO claims(item_id,user_id,proof) VALUES(?,?,?)')->execute([$id,$_SESSION['user']['id'],$_POST['proof']]);
  header('Location: index.php'); exit; }
require 'header.php'; ?>
<h2>Claim request</h2><form method="post" class="card"><input type="hidden" name="id" value="<?= $id ?>">
<textarea name="proof" rows="4" placeholder="Describe proof of ownership (marks, contents, when lost)" required></textarea>
<button>Submit claim</button></form></main></body></html>
