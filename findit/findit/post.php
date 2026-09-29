<?php require 'db.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $img=null;
  if(!empty($_FILES['photo']['name'])){
    $ext=strtolower(pathinfo($_FILES['photo']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['jpg','jpeg','png','webp']) && $_FILES['photo']['size']<2000000){
      $img=uniqid().'.'.$ext; move_uploaded_file($_FILES['photo']['tmp_name'],"uploads/$img"); } }
  $pdo->prepare('INSERT INTO items(user_id,title,category,status,location,photo) VALUES(?,?,?,?,?,?)')
      ->execute([$_SESSION['user']['id'],$_POST['title'],$_POST['category'],$_POST['status'],$_POST['location'],$img]);
  header('Location: index.php'); exit; }
require 'header.php'; ?>
<h2>Post an item</h2>
<form method="post" enctype="multipart/form-data" class="card">
<input name="title" placeholder="Item name" required>
<select name="category"><option>ID/Cards</option><option>Bags</option><option>Electronics</option><option>Other</option></select>
<select name="status"><option value="lost">I lost this</option><option value="found">I found this</option></select>
<input name="location" placeholder="Where?" required>
<input type="file" name="photo" accept="image/*" id="ph"><img id="pv" style="display:none;max-height:120px">
<button>Post</button></form></main>
<script>ph.onchange=()=>{const f=ph.files[0];if(f){pv.src=URL.createObjectURL(f);pv.style.display='block'}}</script></body></html>
