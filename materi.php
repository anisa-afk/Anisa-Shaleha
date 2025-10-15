<!-- siswa/materi.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

// Ambil semua materi beserta nama guru & mata pelajaran
$stmt = $pdo->prepare("
    SELECT m.*, g.nama as nama_guru, g.mata_pelajaran 
    FROM materi m 
    JOIN guru g ON m.guru_id = g.id 
    ORDER BY m.created_at DESC
");
$stmt->execute();
$materi_list = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">📚 Materi Pembelajaran</h1>

    <?php if ($materi_list): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($materi_list as $m): ?>
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition" data-aos="fade-up">
                    <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($m['judul']) ?></h3>
                    <p class="text-gray-600 text-sm mt-2"><?= htmlspecialchars(substr($m['isi'], 0, 120)) ?>...</p>
                    <p class="text-gray-500 text-xs mt-2">
                        Oleh: <strong><?= htmlspecialchars($m['nama_guru']) ?></strong><br>
                        📌 <?= htmlspecialchars($m['mata_pelajaran']) ?>
                    </p>
                    <p class="text-gray-500 text-xs">Tanggal: <?= date('d M Y', strtotime($m['created_at'])) ?></p>

                    <?php if ($m['file_path']): ?>
                        <a href="../uploads/materi/<?= htmlspecialchars(basename($m['file_path'])) ?>" target="_blank"
                            class="mt-4 inline-block text-blue-600 hover:underline text-sm">
                            📄 Lihat File
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada materi tersedia.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>