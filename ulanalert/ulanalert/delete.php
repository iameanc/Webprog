<?php require 'db.php'; if(empty($_SESSION['user'])||$_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(403);exit;}
$id=(int)($_POST['id']??0); if($id<=0){http_response_code(400);exit;}
$stmt=$pdo->prepare('SELECT user_id FROM reports WHERE id=?'); $stmt->execute([$id]); $report=$stmt->fetch();
if(!$report){http_response_code(404);exit;}
$isAdmin=(($_SESSION['user']['role']??'')==='admin'); $isOwner=((int)($_SESSION['user']['id']??0)===(int)$report['user_id']);
if(!$isAdmin&&!$isOwner){http_response_code(403);exit;}
$pdo->prepare('DELETE FROM reports WHERE id=?')->execute([$id]);
