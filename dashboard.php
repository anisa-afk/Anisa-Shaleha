<!-- admin/dashboard.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="kelola_guru.php" class="bg-blue-600 text-white p-6 rounded shadow text-center">
            🧑‍🏫 Kelola Guru
        </a>
        <a href="kelola_siswa.php" class="bg-green-600 text-white p-6 rounded shadow text-center">
            🧑‍🎓 Kelola Siswa
        </a>
        <a href="kelola_jadwal.php" class="bg-purple-600 text-white p-6 rounded shadow text-center">
            📅 Kelola Jadwal
        </a>
    </div>
</div>

<script>AOS.init();</script>
</body>

</html>