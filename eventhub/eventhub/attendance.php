<?php require 'db.php'; require_login(['organizer','admin']); $eid=(int)$_GET['id'];
$chk=$pdo->prepare('SELECT * FROM events WHERE id=?'); $chk->execute([$eid]); $ev=$chk->fetch();
if(!$ev || ($ev['organizer_id']!=$_SESSION['user']['id'] && $_SESSION['user']['role']!=='admin')) die('Not allowed.');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare('UPDATE registrations SET attended=0 WHERE event_id=?')->execute([$eid]);
  foreach(($_POST['present']??[]) as $rid) $pdo->prepare('UPDATE registrations SET attended=1 WHERE id=? AND event_id=?')->execute([(int)$rid,$eid]); }
$rows=$pdo->prepare('SELECT r.id,r.attended,u.name,u.email FROM registrations r JOIN users u ON u.id=r.user_id WHERE r.event_id=? ORDER BY u.name');
$rows->execute([$eid]); $rows=$rows->fetchAll(); require 'header.php'; ?>
<h2><?= e($ev['title']) ?> — Attendance</h2>
<form method="post"><table><tr><th>Name</th><th>Email</th><th>Present</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td>
<td><input type="checkbox" name="present[]" value="<?= $r['id'] ?>" <?= $r['attended']?'checked':'' ?>></td></tr><?php endforeach; ?></table>
<p><?= count(array_filter($rows,fn($r)=>$r['attended'])) ?> / <?= count($rows) ?> present</p>
<button>Save attendance</button> <button type="button" onclick="print()">Print</button></form></main></body></html>
