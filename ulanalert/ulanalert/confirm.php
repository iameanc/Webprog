<?php require 'db.php'; if(empty($_SESSION['user'])||$_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(403);exit;}
try{$pdo->prepare('INSERT INTO confirmations(report_id,user_id) VALUES(?,?)')->execute([(int)$_POST['id'],$_SESSION['user']['id']]);}catch(PDOException $x){} // UNIQUE key: one confirm per user
