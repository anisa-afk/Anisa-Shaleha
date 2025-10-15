<!-- guru/tambah_tugas.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

if ($_POST) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $batas_waktu = $_POST['batas_waktu'];

    $stmt = $pdo->prepare("INSERT INTO tugas (judul, deskripsi, batas_waktu, guru_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$judul, $deskripsi, $batas_waktu, $guru_id]);
    $tugas_id = $pdo->lastInsertId();

    // Upload file instruksi (opsional)
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nama_file = "instruksi_{$guru_id}_{$tugas_id}_" . time() . "." . $ext;
        $path = "../uploads/instruksi/$nama_file";

        if (move_uploaded_file($file['tmp_name'], $path)) {
            $stmt = $pdo->prepare("UPDATE tugas SET file_path = ? WHERE id = ?");
            $stmt->execute([$nama_file, $tugas_id]);
        }
    }

    header("Location: kelola_tugas.php?msg=ditambah");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">➕ Tambah Tugas Baru</h1>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow max-w-3xl">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Judul Tugas</label>
            <input type="text" name="judul" required class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full border rounded-lg px-4 py-2"></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Batas Waktu</label>
            <input type="datetime-local" name="batas_waktu" class="border rounded-lg px-4 py-2">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">File Instruksi (PDF, DOC, dll)</label>
            <input type="file" name="file" accept=".pdf,.doc,.docx,.zip,.jpg"
                class="w-full border border-gray-300 rounded-lg p-2">
        </div>

        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                Simpan Tugas
            </button>
            <a href="kelola_tugas.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>