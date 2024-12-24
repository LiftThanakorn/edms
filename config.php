<?php
// config.php

$host = '10.80.2.11';       // เปลี่ยนเป็นที่อยู่ของฐานข้อมูล
$dbname = 'sql_datapersonne';          // ชื่อฐานข้อมูล
$username = 'sql_datapersonne';        // ชื่อผู้ใช้ฐานข้อมูล
$password = 'd6bd2acd05913';            // รหัสผ่านฐานข้อมูล

date_default_timezone_set('Asia/Bangkok');

try {
    // เชื่อมต่อกับฐานข้อมูล
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
