<!-- guru/tambah_kuis.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

if ($_POST) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $pdo->prepare("INSERT INTO kuis (judul, deskripsi, guru_id) VALUES (?, ?, ?)");
    $stmt->execute([$judul, $deskripsi, $guru_id]);

    $kuis_id = $pdo->lastInsertId();
    header("Location: kelola_soal.php?kuis_id=$kuis_id");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">➕ Buat Kuis Baru</h1>

    <form method="POST" class="bg-white p-6 rounded-xl shadow max-w-3xl">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Judul Kuis</label>
            <input type="text" name="judul" required class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border rounded-lg px-4 py-2"></textarea>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                Lanjutkan → Tambah Soal
            </button>
            <a href="kelola_kuis.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>