<!-- guru/dashboard.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
if (!$guru) {
    die("Data guru tidak ditemukan.");
}

$guru_id = $guru['id'];

// Statistik
$stmt = $pdo->prepare("SELECT COUNT(*) FROM materi WHERE guru_id = ?");
$stmt->execute([$guru_id]);
$total_materi = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM kuis WHERE guru_id = ?");
$stmt->execute([$guru_id]);
$total_kuis = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tugas WHERE guru_id = ?");
$stmt->execute([$guru_id]);
$total_tugas = $stmt->fetchColumn();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">
        Selamat Datang, <?= htmlspecialchars($guru['nama']) ?>!
    </h1>
    <p class="text-gray-600 mb-8">Mata Pelajaran: <strong><?= htmlspecialchars($guru['mata_pelajaran']) ?></strong></p>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-600 text-white p-6 rounded-xl shadow text-center" data-aos="flip-up">
            <h2 class="text-xl font-semibold">Materi</h2>
            <p class="text-4xl font-bold mt-2"><?= $total_materi ?></p>
        </div>
        <div class="bg-indigo-600 text-white p-6 rounded-xl shadow text-center" data-aos="flip-up" data-aos-delay="300">
            <h2 class="text-xl font-semibold">Kuis</h2>
            <p class="text-4xl font-bold mt-2"><?= $total_kuis ?></p>
        </div>
        <div class="bg-purple-600 text-white p-6 rounded-xl shadow text-center" data-aos="flip-up" data-aos-delay="500">
            <h2 class="text-xl font-semibold">Tugas</h2>
            <p class="text-4xl font-bold mt-2"><?= $total_tugas ?></p>
        </div>
    </div>

    <!-- Akses Cepat -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="kelola_materi.php"
            class="bg-gray-700 hover:bg-gray-800 text-white p-6 rounded-xl shadow text-center transition">
            📚 Kelola Materi
        </a>
        <a href="kelola_kuis.php"
            class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-xl shadow text-center transition">
            🧪 Kelola Kuis
        </a>
        <a href="kelola_tugas.php"
            class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-xl shadow text-center transition">
            📁 Kelola Tugas
        </a>
        <a href="nilai_tugas.php"
            class="bg-yellow-500 hover:bg-yellow-600 text-white p-6 rounded-xl shadow text-center transition">
            ✅ Nilai Tugas
        </a>
    </div>
</div>

<script>AOS.init();</script>
</body>

</html>