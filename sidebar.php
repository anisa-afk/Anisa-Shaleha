<!-- includes/sidebar.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$user_role = $_SESSION['role'];
$user_nama = $_SESSION['nama'];
?>

<aside class="w-64 bg-blue-800 text-white h-full fixed min-h-screen p-4 shadow-lg" data-aos="fade-right">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">E-Learning</h2>
        <p class="text-sm text-blue-200">Halo, <?= htmlspecialchars($user_nama) ?></p>
        <span class="text-xs bg-blue-600 px-2 py-1 rounded"><?= ucfirst($user_role) ?></span>
    </div>

    <nav class="space-y-2">
        <?php if ($user_role === 'siswa'): ?>
            <a href="../siswa/dashboard.php" class="block p-3 rounded hover:bg-blue-700 transition">🏠 Dashboard</a>
            <a href="../siswa/materi.php" class="block p-3 rounded hover:bg-blue-700 transition">📚 Materi</a>
            <a href="../siswa/kuis.php" class="block p-3 rounded hover:bg-blue-700 transition">📝 Kuis</a>
            <a href="../siswa/tugas.php" class="block p-3 rounded hover:bg-blue-700 transition">📋 Tugas</a>
            <a href="../siswa/leaderboard.php" class="block p-3 rounded hover:bg-blue-700 transition">🏆 Leaderboard</a>
            <a href="../siswa/jadwal.php" class="block p-3 rounded hover:bg-blue-700">📅 Jadwal</a>
            <a href="../siswa/profil.php" class="block p-3 rounded hover:bg-blue-700 transition">👤 Profil</a>
        <?php elseif ($user_role === 'guru'): ?>
            <a href="../guru/dashboard.php" class="block p-3 rounded hover:bg-blue-700 transition">🏠 Dashboard</a>
            <a href="../guru/kelola_materi.php" class="block p-3 rounded hover:bg-blue-700 transition">📚 Kelola Materi</a>
            <a href="../guru/kelola_kuis.php" class="block p-3 rounded hover:bg-blue-700 transition">🧪 Kelola Kuis</a>
            <a href="../guru/kelola_tugas.php" class="block p-3 rounded hover:bg-blue-700 transition">📁 Kelola Tugas</a>
            <a href="../guru/nilai_tugas.php" class="block p-3 rounded hover:bg-blue-700 transition">✅ Nilai Tugas</a>
            <a href="../guru/lihat_hasil_kuis.php" class="block p-3 rounded hover:bg-blue-700 transition">📊 Hasil Kuis</a>
            <a href="../guru/jadwal.php" class="block p-3 rounded hover:bg-blue-700">📅 Jadwal</a>
            <a href="../guru/laporan.php" class="block p-3 rounded hover:bg-blue-700 transition">📊 Laporan</a>
        <?php elseif ($user_role === 'admin'): ?>
            <a href="../admin/dashboard.php" class="block p-3 rounded hover:bg-blue-700">🏠 Dashboard Admin</a>
            <a href="../admin/kelola_guru.php" class="block p-3 rounded hover:bg-blue-700">🧑‍🏫 Kelola Guru</a>
            <a href="../admin/kelola_siswa.php" class="block p-3 rounded hover:bg-blue-700">🧑‍🎓 Kelola Siswa</a>
            <a href="../admin/kelola_jadwal.php" class="block p-3 rounded hover:bg-blue-700">📅 Kelola Jadwal</a>
        <?php endif; ?>
        <a href="../auth/logout.php" class="block p-3 rounded hover:bg-red-700 transition text-red-200 mt-6">🚪
            Logout</a>
    </nav>
</aside>