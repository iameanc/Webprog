<?php require 'db.php'; if(($_SESSION['user']['role']??'')!=='admin'||$_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(403);exit;}
$pdo->prepare('DELETE FROM reports WHERE id=?')->execute([(int)$_POST['id']]);
