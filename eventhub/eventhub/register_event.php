<?php require 'db.php'; require_login();
$eid=(int)$_POST['event_id'];
try{
  $pdo->beginTransaction();
  $ev=$pdo->prepare('SELECT slots FROM events WHERE id=? FOR UPDATE'); $ev->execute([$eid]); $slots=$ev->fetchColumn();
  $c=$pdo->prepare('SELECT COUNT(*) FROM registrations WHERE event_id=?'); $c->execute([$eid]);
  if($slots===false || $c->fetchColumn()>=$slots) throw new Exception('This event is full.');
  $pdo->prepare('INSERT INTO registrations(event_id,user_id) VALUES(?,?)')->execute([$eid,$_SESSION['user']['id']]); // UNIQUE key blocks duplicates
  $pdo->commit(); $msg='Registered!';
}catch(PDOException $x){ $pdo->rollBack(); $msg='You are already registered.'; }
 catch(Exception $x){ $pdo->rollBack(); $msg=$x->getMessage(); }
require 'header.php'; echo '<p>'.e($msg).'</p><a href="index.php">Back to events</a></main></body></html>';
