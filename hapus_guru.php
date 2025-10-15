<!-- admin/hapus_guru.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kelola_guru.php");
    exit;
}

$id = $_GET['id'];

try {
    $pdo->beginTransaction();

    // Ambil user_id dari guru
    $stmt = $pdo->prepare("SELECT user_id FROM guru WHERE id = ?");
    $stmt->execute([$id]);
    $guru = $stmt->fetch();

    if (!$guru) {
        throw new Exception("Guru tidak ditemukan.");
    }

    // Hapus dari guru
    $stmt = $pdo->prepare("DELETE FROM guru WHERE id = ?");
    $stmt->execute([$id]);

    // Hapus dari users
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$guru['user_id']]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollback();
    // Tidak tampilkan error detail ke user
}

// Redirect tanpa peduli berhasil atau tidak
header("Location: kelola_guru.php");
exit;