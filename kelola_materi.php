<!-- guru/kelola_materi.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

// Ambil materi milik guru
$stmt = $pdo->prepare("SELECT * FROM materi WHERE guru_id = ? ORDER BY created_at DESC");
$stmt->execute([$guru_id]);
$materi_list = $stmt->fetchAll();

// Hapus materi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("SELECT file_path FROM materi WHERE id = ? AND guru_id = ?");
    $stmt->execute([$id, $guru_id]);
    $materi = $stmt->fetch();

    if ($materi) {
        // Hapus file jika ada
        if ($materi['file_path']) {
            $file = "../uploads/materi/" . basename($materi['file_path']);
            if (file_exists($file))
                unlink($file);
        }
        $stmt = $pdo->prepare("DELETE FROM materi WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: kelola_materi.php?msg=dihapus");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📚 Materi Saya</h1>
        <a href="tambah_materi.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">➕ Tambah
            Materi</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'dihapus'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">Materi berhasil dihapus.
        </div>
    <?php endif; ?>

    <?php if ($materi_list): ?>
        <div class="space-y-6">
            <?php foreach ($materi_list as $m): ?>
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($m['judul']) ?></h3>
                    <p class="text-gray-600 mt-2"><?= htmlspecialchars(substr($m['isi'], 0, 200)) ?>...</p>
                    <p class="text-gray-500 text-sm">Dibuat: <?= date('d M Y', strtotime($m['created_at'])) ?></p>

                    <?php if ($m['file_path']): ?>
                        <a href="../uploads/materi/<?= htmlspecialchars(basename($m['file_path'])) ?>" target="_blank"
                            class="text-blue-600 text-sm hover:underline">📄 Lihat File</a>
                    <?php endif; ?>

                    <div class="mt-4 space-x-2">
                        <a href="tambah_materi.php?edit=<?= $m['id'] ?>" class="text-indigo-600 text-sm hover:underline">✏️
                            Edit</a>
                        <a href="?hapus=<?= $m['id'] ?>" onclick="return confirm('Hapus materi ini?')"
                            class="text-red-600 text-sm hover:underline">🗑️ Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada materi. Klik tombol di atas untuk menambahkan.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>