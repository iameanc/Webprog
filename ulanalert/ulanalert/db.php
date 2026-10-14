<?php
session_start();
require __DIR__.'/config.php';
$pdo = new PDO('mysql:host=localhost;dbname='.DB_NAME.';charset=utf8mb4','root','',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }
function require_login($roles=null){
  if(empty($_SESSION['user']) || ($roles && !in_array($_SESSION['user']['role'],(array)$roles))){
    header('Location: login.php'); exit; }
}
