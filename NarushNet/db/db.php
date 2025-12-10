<?php
session_start();

$db = mysqli_connect("localhost", "root", "", "NarushNet");
if (!$db) {
    die ("Ошибка подключения к базе данных");
}

function find($login, $password) {
  global $db;
  $result = mysqli_query($db, "SELECT * FROM user WHERE username = '$login' AND password = MD5('$password');");
  return mysqli_fetch_assoc($result); // Вернем объект записи
}

?>