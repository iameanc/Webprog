<?php require 'db.php'; header('Content-Type: application/json');
if(empty($_SESSION['user'])){ echo json_encode(['ok'=>false,'error'=>'Please log in first.']); exit; }
$in=json_decode(file_get_contents('php://input'),true);
try{
  $pdo->beginTransaction(); $total=0; $lines=[];
  foreach($in['items'] as $it){ // prices come from the DB, never from the browser
    $s=$pdo->prepare('SELECT id,price FROM menu_items WHERE id=? AND available=1'); $s->execute([(int)$it['id']]); $m=$s->fetch();
    $qty=max(1,min(20,(int)$it['qty'])); if(!$m) throw new Exception('Item unavailable.');
    $total+=$m['price']*$qty; $lines[]=[$m['id'],$qty,$m['price']]; }
  $pdo->prepare('INSERT INTO orders(user_id,total,pickup_time) VALUES(?,?,?)')->execute([$_SESSION['user']['id'],$total,$in['pickup']]);
  $oid=$pdo->lastInsertId();
  foreach($lines as $l) $pdo->prepare('INSERT INTO order_items(order_id,menu_item_id,qty,price) VALUES(?,?,?,?)')->execute([$oid,...$l]);
  $pdo->commit(); echo json_encode(['ok'=>true,'order_id'=>$oid]);
}catch(Exception $x){ $pdo->rollBack(); echo json_encode(['ok'=>false,'error'=>$x->getMessage()]); }
