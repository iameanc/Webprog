<?php require 'db.php';
$s=$pdo->prepare("SELECT * FROM items WHERE status<>'returned' AND (title LIKE ? OR location LIKE ?) AND (?='' OR status=?) ORDER BY id DESC");
$k='%'.($_GET['q']??'').'%'; $st=$_GET['status']??''; $s->execute([$k,$k,$st,$st]);
$rows=$s->fetchAll(); if(!$rows) echo '<p>No items found.</p>';
foreach($rows as $i): ?>
<div class="card"><?php if($i['photo']) echo '<img src="uploads/'.e($i['photo']).'" alt="">'; ?>
<b><?= e($i['title']) ?></b><span class="badge"><?= e($i['status']) ?></span>
<small><?= e($i['location']) ?></small>
<?php if($i['status']==='found'): ?><a href="claim.php?id=<?= $i['id'] ?>">Claim this item</a><?php endif; ?></div>
<?php endforeach;
