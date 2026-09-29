<?php require 'db.php';
$s=$pdo->prepare("SELECT e.*, e.slots-(SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id) AS left_slots
 FROM events e WHERE e.event_date>=NOW() AND (e.title LIKE ? OR e.venue LIKE ?) AND (?='' OR DATE(e.event_date)=?) ORDER BY e.event_date");
$k='%'.($_GET['q']??'').'%'; $d=$_GET['date']??''; $s->execute([$k,$k,$d,$d]); $rows=$s->fetchAll();
if(!$rows) echo '<p>No upcoming events.</p>';
foreach($rows as $ev): ?>
<div class="card"><?php if($ev['poster']) echo '<img src="uploads/'.e($ev['poster']).'" alt="">'; ?>
<b><?= e($ev['title']) ?></b><small><?= date('M j, g:i A',strtotime($ev['event_date'])) ?> · <?= e($ev['venue']) ?></small>
<span class="badge"><?= $ev['left_slots']>0 ? $ev['left_slots'].' slots left' : 'FULL' ?></span>
<?php if($ev['left_slots']>0): ?><form method="post" action="register_event.php"><input type="hidden" name="event_id" value="<?= $ev['id'] ?>"><button>Register</button></form><?php endif; ?>
<?php if(($_SESSION['user']['id']??0)==$ev['organizer_id']): ?><a href="attendance.php?id=<?= $ev['id'] ?>">Attendance</a><?php endif; ?></div>
<?php endforeach;
