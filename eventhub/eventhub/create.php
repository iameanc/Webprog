<?php require 'db.php'; require_login(['organizer','admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){
  $img=null;
  if(!empty($_FILES['poster']['name'])){
    $ext=strtolower(pathinfo($_FILES['poster']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['jpg','jpeg','png','webp']) && $_FILES['poster']['size']<2000000){
      $img=uniqid().'.'.$ext; move_uploaded_file($_FILES['poster']['tmp_name'],"uploads/$img"); } }
  $pdo->prepare('INSERT INTO events(organizer_id,title,description,venue,event_date,slots,poster) VALUES(?,?,?,?,?,?,?)')
      ->execute([$_SESSION['user']['id'],$_POST['title'],$_POST['description'],$_POST['venue'],$_POST['event_date'],max(1,(int)$_POST['slots']),$img]);
  header('Location: index.php'); exit; }
require 'header.php'; ?>
<h2>Create event</h2><form method="post" enctype="multipart/form-data" class="card">
<input name="title" placeholder="Title" required><textarea name="description" rows="3" placeholder="Description"></textarea>
<input name="venue" placeholder="Venue" required><input type="datetime-local" name="event_date" required>
<input type="number" name="slots" min="1" value="50"><input type="file" name="poster" accept="image/*"><button>Publish</button></form></main></body></html>
