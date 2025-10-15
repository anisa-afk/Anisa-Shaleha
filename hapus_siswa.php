<!-- admin/hapus_siswa.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kelola_siswa.php");
    exit;
}

$id = $_GET['id'];

try {
    $pdo->beginTransaction();

    // Ambil user_id dari siswa
    $stmt = $pdo->prepare("SELECT user_id FROM siswa WHERE id = ?");
    $stmt->execute([$id]);
    $siswa = $stmt->fetch();

    if (!$siswa) {
        throw new Exception("Siswa tidak ditemukan.");
    }

    // Hapus dari siswa
    $stmt = $pdo->prepare("DELETE FROM siswa WHERE id = ?");
    $stmt->execute([$id]);

    // Hapus dari users
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$siswa['user_id']]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollback();
    // Tidak tampilkan error detail
}

// Redirect
header("Location: kelola_siswa.php");
exit;