<!-- siswa/profil.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$siswa = get_siswa_data($pdo);
if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

$siswa_id = $siswa['id'];
$total_poin = get_user_poin($siswa_id, $pdo);
$level_saat_ini = get_user_level($siswa_id, $pdo);

// Ambil jumlah tugas & kuis yang sudah dikerjakan
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pengumpulan_tugas WHERE siswa_id = ?");
$stmt->execute([$siswa_id]);
$jml_tugas = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT kuis_id) FROM jawaban_siswa WHERE siswa_id = ?");
$stmt->execute([$siswa_id]);
$jml_kuis = $stmt->fetchColumn();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">👤 Profil Saya</h1>

    <div class="bg-white p-8 rounded-xl shadow max-w-3xl" data-aos="fade-up">
        <!-- Informasi Utama -->
        <div class="flex items-center mb-6">
            <div
                class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                <?= strtoupper(substr($siswa['nama'], 0, 1)) ?>
            </div>
            <div class="ml-6">
                <h2 class="text-2xl font-semibold"><?= htmlspecialchars($siswa['nama']) ?></h2>
                <p class="text-gray-600">NIS: <?= htmlspecialchars($siswa['nis']) ?> | Kelas:
                    <?= htmlspecialchars($siswa['kelas']) ?>
                </p>
            </div>
        </div>

        <!-- Detail Profil -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Informasi Pribadi</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Nama:</strong> <?= htmlspecialchars($siswa['nama']) ?></li>
                    <li><strong>NIS:</strong> <?= htmlspecialchars($siswa['nis']) ?></li>
                    <li><strong>Kelas:</strong> <?= htmlspecialchars($siswa['kelas']) ?></li>
                    <li><strong>Angkatan:</strong> <?= $siswa['angkatan'] ?></li>
                    <li><strong>Jenis Kelamin:</strong>
                        <?= $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Kontak</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></li>
                    <li><strong>Telepon:</strong> <?= htmlspecialchars($siswa['telepon'] ?? '-') ?></li>
                    <li><strong>Alamat:</strong> <?= htmlspecialchars($siswa['alamat'] ?? '-') ?></li>
                </ul>
            </div>
        </div>

        <!-- Statistik Gamifikasi -->
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">📊 Statistik Pembelajaran</h3>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-blue-600"><?= $jml_kuis ?></p>
                    <p class="text-sm text-gray-600">Kuis Dikerjakan</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= $jml_tugas ?></p>
                    <p class="text-sm text-gray-600">Tugas Dikumpulkan</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-yellow-600"><?= $total_poin ?></p>
                    <p class="text-sm text-gray-600">Total Poin</p>
                </div>
            </div>
            <p class="mt-3 text-sm text-center">
                Level: <strong><?= htmlspecialchars($level_saat_ini) ?></strong>
            </p>
        </div>
    </div>
</div>

<script>AOS.init();</script>
</body>

</html>