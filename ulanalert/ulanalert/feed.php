<?php require 'db.php';
$sev=$_GET['severity']??'';
$s=$pdo->prepare("SELECT r.*,u.name,(SELECT COUNT(*) FROM confirmations c WHERE c.report_id=r.id) AS conf
 FROM reports r JOIN users u ON u.id=r.user_id WHERE r.created_at>NOW()-INTERVAL 24 HOUR AND (?='' OR r.severity=?) ORDER BY r.id DESC LIMIT 50");
$s->execute([$sev,$sev]); $rows=$s->fetchAll(); if(!$rows) echo '<p>No reports in the last 24 hours.</p>';
foreach($rows as $r): ?>
<div class="card"><span class="badge <?= e($r['severity']) ?>"><?= e($r['severity']) ?></span>
<b><?= e($r['location']) ?></b><?php if($r['notes']) echo '<small>'.e($r['notes']).'</small>'; ?>
<small>by <?= e($r['name']) ?> · <?= date('g:i A',strtotime($r['created_at'])) ?> · <?= (int)$r['conf'] ?> confirmed</small>
<?php if(!empty($_SESSION['user'])): $canDelete=(($_SESSION['user']['role']??'')==='admin') || ((int)($_SESSION['user']['id']??0)===(int)$r['user_id']); ?>
<button onclick="act('confirm.php',<?= $r['id'] ?>)">I see this too</button>
<?php if($canDelete): ?><button onclick="if(confirm('Delete this report?'))act('delete.php',<?= $r['id'] ?>)">Delete</button><?php endif; ?>
<?php endif; ?></div>
<?php endforeach;
