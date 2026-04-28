<?php
$host = 'localhost'; 
$dbname = 'j95389rd_2';
$user = 'j95389rd_2';
$pass = 'catcat123A';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

session_start();
?>