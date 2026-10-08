<?php require 'db.php'; if(empty($_SESSION['user'])||$_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(403);exit;}
$sev=in_array($_POST['severity'],['Light','Heavy','Flooded'])?$_POST['severity']:'Light';
$pdo->prepare('INSERT INTO reports(user_id,location,severity,notes) VALUES(?,?,?,?)')
    ->execute([$_SESSION['user']['id'],mb_substr(trim($_POST['location']),0,120),$sev,mb_substr(trim($_POST['notes']??''),0,255)]);
