<!-- guru/tambah_materi.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

$materi = null;
$edit = false;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM materi WHERE id = ? AND guru_id = ?");
    $stmt->execute([$id, $guru_id]);
    $materi = $stmt->fetch();
    $edit = true;
}

if ($_POST) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];

    if ($edit) {
        $stmt = $pdo->prepare("UPDATE materi SET judul = ?, isi = ? WHERE id = ? AND guru_id = ?");
        $stmt->execute([$judul, $isi, $_GET['edit'], $guru_id]);

        // Upload file baru (opsional)
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file = $_FILES['file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nama_file = "materi_{$guru_id}_" . time() . "." . $ext;
            $path = "../uploads/materi/$nama_file";

            if (move_uploaded_file($file['tmp_name'], $path)) {
                // Hapus file lama
                if ($materi['file_path']) {
                    $old = "../uploads/materi/" . basename($materi['file_path']);
                    if (file_exists($old))
                        unlink($old);
                }
                $stmt = $pdo->prepare("UPDATE materi SET file_path = ? WHERE id = ?");
                $stmt->execute([$nama_file, $_GET['edit']]);
            }
        }

        header("Location: kelola_materi.php?msg=diupdate");
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO materi (judul, isi, guru_id) VALUES (?, ?, ?)");
        $stmt->execute([$judul, $isi, $guru_id]);
        $materi_id = $pdo->lastInsertId();

        // Upload file
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file = $_FILES['file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nama_file = "materi_{$guru_id}_{$materi_id}_" . time() . "." . $ext;
            $path = "../uploads/materi/$nama_file";

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $stmt = $pdo->prepare("UPDATE materi SET file_path = ? WHERE id = ?");
                $stmt->execute([$nama_file, $materi_id]);
            }
        }

        header("Location: kelola_materi.php?msg=ditambah");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <?= $edit ? '✏️ Edit Materi' : '➕ Tambah Materi Baru' ?>
    </h1>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow max-w-3xl">
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Judul</label>
            <input type="text" name="judul" required value="<?= htmlspecialchars($materi['judul'] ?? '') ?>"
                class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Isi Materi</label>
            <textarea name="isi" rows="6"
                class="w-full border rounded-lg px-4 py-2"><?= htmlspecialchars($materi['isi'] ?? '') ?></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">File (PDF, PPT, ZIP, dll)</label>
            <input type="file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx,.zip,.jpg,.png"
                class="w-full border border-gray-300 rounded-lg p-2">
            <?php if ($edit && $materi['file_path']): ?>
                <p class="text-sm text-gray-500 mt-1">
                    File saat ini: <a href="../uploads/materi/<?= htmlspecialchars(basename($materi['file_path'])) ?>"
                        target="_blank" class="text-blue-600">Lihat</a>
                </p>
            <?php endif; ?>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                <?= $edit ? '💾 Update' : '➕ Simpan Materi' ?>
            </button>
            <a href="kelola_materi.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>