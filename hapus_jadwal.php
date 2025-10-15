<!-- admin/hapus_jadwal.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kelola_jadwal.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM jadwal WHERE id = ?");
$stmt->execute([$id]);

header("Location: kelola_jadwal.php");
exit;