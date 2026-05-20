<?php
$host = "localhost";
$db_name = "db_api_tester";
$username = "root";
$password = ""; // Kosongkan jika menggunakan bawaan XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    // Set error mode ke exception untuk mempermudah debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    echo "Koneksi database gagal: " . $exception->getMessage();
    exit;
}
?>
