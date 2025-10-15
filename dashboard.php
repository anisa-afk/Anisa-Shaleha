<!-- siswa/dashboard.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

// Ambil data siswa
$siswa = get_siswa_data($pdo);
if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

$siswa_id = $siswa['id'];
$total_poin = get_user_poin($siswa_id, $pdo);
$level_saat_ini = get_user_level($siswa_id, $pdo);

// Ambil jumlah lencana yang dimiliki
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM lencana_siswa WHERE siswa_id = ?");
$stmt->execute([$siswa_id]);
$total_lencana = $stmt->fetch()['total'];

// Ambil 3 lencana terakhir
$stmt = $pdo->prepare("
    SELECT l.nama, l.gambar_path 
    FROM lencana_siswa ls 
    JOIN lencana l ON ls.lencana_id = l.id 
    WHERE ls.siswa_id = ? 
    ORDER BY ls.tanggal_diterima DESC 
    LIMIT 3
");
$stmt->execute([$siswa_id]);
$lencana_terakhir = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<!-- Konten Utama -->
<div class="ml-64 p-6 flex-1">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <h1 class="text-3xl font-bold text-gray-800 mb-2" data-aos="fade-down">
            Selamat Datang, <?= htmlspecialchars($siswa['nama']) ?>!
        </h1>
        <p class="text-gray-600 mb-8" data-aos="fade-down" data-aos-delay="200">
            Kelas: <strong><?= htmlspecialchars($siswa['kelas']) ?></strong> | Angkatan: <?= $siswa['angkatan'] ?>
        </p>

        <!-- Statistik Gamifikasi -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Poin -->
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-6 rounded-xl shadow-lg text-center"
                data-aos="flip-up">
                <h2 class="text-xl font-semibold">Total Poin</h2>
                <p class="text-4xl font-bold mt-2"><?= $total_poin ?></p>
                <p class="text-sm opacity-90">Poin dari kuis & tugas</p>
            </div>

            <!-- Level -->
            <div class="bg-gradient-to-r from-green-400 to-teal-500 text-white p-6 rounded-xl shadow-lg text-center"
                data-aos="flip-up" data-aos-delay="300">
                <h2 class="text-xl font-semibold">Level</h2>
                <p class="text-4xl font-bold mt-2"><?= htmlspecialchars($level_saat_ini) ?></p>
                <p class="text-sm opacity-90">Tingkat kemampuanmu</p>
            </div>

            <!-- Lencana -->
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-xl shadow-lg text-center"
                data-aos="flip-up" data-aos-delay="500">
                <h2 class="text-xl font-semibold">Lencana</h2>
                <p class="text-4xl font-bold mt-2"><?= $total_lencana ?></p>
                <p class="text-sm opacity-90">Prestasi yang diraih</p>
            </div>
        </div>

        <!-- Lencana Terakhir -->
        <div class="bg-white p-6 rounded-xl shadow-md mb-8" data-aos="fade-up">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Lencana Terbaru 🏆</h3>
            <?php if ($lencana_terakhir): ?>
                <div class="flex space-x-6">
                    <?php foreach ($lencana_terakhir as $lencana): ?>
                        <div class="text-center">
                            <img src="../assets/lencana/<?= htmlspecialchars($lencana['gambar_path']) ?>"
                                alt="<?= htmlspecialchars($lencana['nama']) ?>"
                                class="w-16 h-16 object-cover rounded-full mx-auto mb-2 shadow">
                            <p class="text-sm font-medium"><?= htmlspecialchars($lencana['nama']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Belum ada lencana. Ayo selesaikan kuis dan tugas!</p>
            <?php endif; ?>
        </div>

        <!-- Akses Cepat -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="materi.php"
                class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-xl shadow text-center transition"
                data-aos="zoom-in">
                <h3 class="text-xl font-bold">📚 Materi</h3>
                <p class="mt-2">Pelajari materi pelajaran</p>
            </a>
            <a href="kuis.php"
                class="bg-indigo-600 hover:bg-indigo-700 text-white p-6 rounded-xl shadow text-center transition"
                data-aos="zoom-in" data-aos-delay="200">
                <h3 class="text-xl font-bold">📝 Kuis</h3>
                <p class="mt-2">Uji pemahamanmu</p>
            </a>
            <a href="tugas.php"
                class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-xl shadow text-center transition"
                data-aos="zoom-in" data-aos-delay="400">
                <h3 class="text-xl font-bold">📋 Tugas</h3>
                <p class="mt-2">Kerjakan tugas terbaru</p>
            </a>
        </div>
    </div>
</div>

<script>
    AOS.init({ duration: 800, easing: 'ease-in-out' });
</script>
</body>

</html>