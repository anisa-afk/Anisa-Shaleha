<!-- guru/kelola_kuis.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

// Ambil daftar kuis
$stmt = $pdo->prepare("SELECT * FROM kuis WHERE guru_id = ? ORDER BY created_at DESC");
$stmt->execute([$guru_id]);
$kuis_list = $stmt->fetchAll();

// Hapus kuis
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("SELECT id FROM kuis WHERE id = ? AND guru_id = ?");
    $stmt->execute([$id, $guru_id]);
    if ($stmt->fetch()) {
        // Hapus soal terkait
        $stmt = $pdo->prepare("DELETE FROM soal WHERE kuis_id = ?");
        $stmt->execute([$id]);
        // Hapus kuis
        $stmt = $pdo->prepare("DELETE FROM kuis WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: kelola_kuis.php?msg=dihapus");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">🧪 Kuis Saya</h1>
        <a href="tambah_kuis.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">➕ Buat Kuis
            Baru</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'dihapus'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">Kuis berhasil dihapus.</div>
    <?php endif; ?>

    <?php if ($kuis_list): ?>
        <div class="space-y-6">
            <?php foreach ($kuis_list as $k): ?>
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($k['judul']) ?></h3>
                    <p class="text-gray-600 mt-2"><?= htmlspecialchars($k['deskripsi']) ?></p>
                    <p class="text-gray-500 text-sm">Dibuat: <?= date('d M Y', strtotime($k['created_at'])) ?></p>

                    <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM soal WHERE kuis_id = ?");
                    $stmt->execute([$k['id']]);
                    $jumlah_soal = $stmt->fetchColumn();
                    ?>

                    <p class="text-sm text-gray-500">Soal: <?= $jumlah_soal ?> butir</p>

                    <div class="mt-4 space-x-2">
                        <a href="kelola_soal.php?kuis_id=<?= $k['id'] ?>" class="text-indigo-600 text-sm hover:underline">➕
                            Kelola Soal</a>
                        <a href="?hapus=<?= $k['id'] ?>" onclick="return confirm('Hapus kuis dan semua soalnya?')"
                            class="text-red-600 text-sm hover:underline">🗑️ Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada kuis. Klik tombol di atas untuk membuat.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>