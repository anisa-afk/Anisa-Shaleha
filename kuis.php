<!-- siswa/kuis.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$siswa_id = get_siswa_data($pdo)['id'];

// Ambil daftar kuis + guru + mata pelajaran
$stmt = $pdo->prepare("
    SELECT k.*, g.nama as nama_guru, g.mata_pelajaran 
    FROM kuis k 
    JOIN guru g ON k.guru_id = g.id 
    ORDER BY k.created_at DESC
");
$stmt->execute();
$kuis_list = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">📝 Daftar Kuis</h1>

    <?php if ($kuis_list): ?>
        <div class="space-y-6">
            <?php foreach ($kuis_list as $k): ?>
                <div class="bg-white p-6 rounded-xl shadow" data-aos="fade-up">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($k['judul']) ?></h3>
                    <p class="text-gray-600 mt-2"><?= htmlspecialchars($k['deskripsi']) ?></p>
                    <p class="text-gray-500 text-sm">
                        🧑‍🏫 <?= htmlspecialchars($k['nama_guru']) ?> |
                        📌 <?= htmlspecialchars($k['mata_pelajaran']) ?>
                    </p>

                    <a href="kerjakan_kuis.php?id=<?= $k['id'] ?>"
                        class="mt-4 inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition">
                        ➡️ Kerjakan Kuis
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada kuis tersedia.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>