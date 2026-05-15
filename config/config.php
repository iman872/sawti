<?php
// config.php - الملف الرئيسي للاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'sawti_db';
$username = 'root';
$password = '';  // تأكد أن كلمة المرور فارغة في XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8mb4");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
} catch (PDOException $e) {
    die("❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>