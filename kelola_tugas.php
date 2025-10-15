<!-- guru/kelola_tugas.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

$stmt = $pdo->prepare("SELECT * FROM tugas WHERE guru_id = ? ORDER BY batas_waktu DESC");
$stmt->execute([$guru_id]);
$tugas_list = $stmt->fetchAll();

// Hapus tugas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("SELECT file_path FROM tugas WHERE id = ? AND guru_id = ?");
    $stmt->execute([$id, $guru_id]);
    $tugas = $stmt->fetch();

    if ($tugas) {
        if ($tugas['file_path']) {
            $file = "../uploads/instruksi/" . basename($tugas['file_path']);
            if (file_exists($file))
                unlink($file);
        }
        $stmt = $pdo->prepare("DELETE FROM tugas WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: kelola_tugas.php?msg=dihapus");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📁 Tugas Saya</h1>
        <a href="tambah_tugas.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">➕ Tambah Tugas</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'dihapus'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">Tugas berhasil dihapus.
        </div>
    <?php endif; ?>

    <?php if ($tugas_list): ?>
        <div class="space-y-6">
            <?php foreach ($tugas_list as $t): ?>
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($t['judul']) ?></h3>
                    <p class="text-gray-600 mt-2"><?= htmlspecialchars(substr($t['deskripsi'], 0, 150)) ?>...</p>
                    <p class="text-gray-500 text-sm">
                        Batas: <?= $t['batas_waktu'] ? date('d M Y H:i', strtotime($t['batas_waktu'])) : 'Tidak ada' ?>
                    </p>

                    <?php if ($t['file_path']): ?>
                        <a href="../uploads/instruksi/<?= htmlspecialchars(basename($t['file_path'])) ?>" target="_blank"
                            class="text-blue-600 text-sm hover:underline">📄 Instruksi</a>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="?hapus=<?= $t['id'] ?>" onclick="return confirm('Hapus tugas ini?')"
                            class="text-red-600 text-sm hover:underline">🗑️ Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada tugas. Klik tombol di atas untuk menambahkan.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>