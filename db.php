<!-- includes/db.php -->
<?php
$host = 'localhost';
$dbname = 'e_learning';
$username = 'root';
$password = ''; // Sesuaikan dengan setting Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>