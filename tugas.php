<!-- siswa/tugas.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$siswa_id = get_siswa_data($pdo)['id'];

// Ambil daftar tugas + guru + mata pelajaran
$stmt = $pdo->prepare("
    SELECT t.*, g.nama as nama_guru, g.mata_pelajaran,
           pt.nilai, pt.waktu_kumpul 
    FROM tugas t
    JOIN guru g ON t.guru_id = g.id
    LEFT JOIN pengumpulan_tugas pt ON t.id = pt.tugas_id AND pt.siswa_id = ?
    ORDER BY t.batas_waktu ASC
");
$stmt->execute([$siswa_id]);
$tugas_list = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">📋 Daftar Tugas</h1>

    <?php if ($tugas_list): ?>
        <div class="space-y-6">
            <?php foreach ($tugas_list as $t): ?>
                <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($t['judul']) ?></h3>
                    <p class="text-gray-600 mt-2"><?= htmlspecialchars(substr($t['deskripsi'], 0, 150)) ?>...</p>
                    <p class="text-gray-500 text-sm">
                        🧑‍🏫 <?= htmlspecialchars($t['nama_guru']) ?> |
                        📌 <?= htmlspecialchars($t['mata_pelajaran']) ?>
                    </p>
                    <p class="text-gray-500 text-sm">
                        Batas Waktu:
                        <strong><?= $t['batas_waktu'] ? date('d M Y H:i', strtotime($t['batas_waktu'])) : 'Tidak ada' ?></strong>
                    </p>

                    <?php if ($t['file_path']): ?>
                        <a href="../uploads/instruksi/<?= htmlspecialchars(basename($t['file_path'])) ?>" target="_blank"
                            class="mt-2 inline-block text-blue-600 text-sm hover:underline">
                            📄 Lihat Instruksi
                        </a>
                    <?php endif; ?>

                    <div class="mt-4">
                        <?php if ($t['waktu_kumpul']): ?>
                            <span class="text-green-600 text-sm">✅ Sudah dikumpulkan</span>
                            <?php if ($t['nilai']): ?>
                                <p class="text-sm text-gray-700 mt-1">Nilai: <strong><?= $t['nilai'] ?></strong></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="kumpulkan_tugas.php?tugas_id=<?= $t['id'] ?>"
                                class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">
                                📤 Kumpulkan Tugas
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada tugas tersedia.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>