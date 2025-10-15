<!-- siswa/kumpulkan_tugas.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

if (!isset($_GET['tugas_id'])) {
    header("Location: tugas.php");
    exit;
}

$tugas_id = $_GET['tugas_id'];
$siswa_id = get_siswa_data($pdo)['id'];

// Cek apakah sudah mengumpulkan
$stmt = $pdo->prepare("SELECT id FROM pengumpulan_tugas WHERE tugas_id = ? AND siswa_id = ?");
$stmt->execute([$tugas_id, $siswa_id]);
if ($stmt->fetch()) {
    die("<p>Kamu sudah mengumpulkan tugas ini.</p>");
}

// Ambil info tugas
$stmt = $pdo->prepare("SELECT judul, batas_waktu FROM tugas WHERE id = ?");
$stmt->execute([$tugas_id]);
$tugas = $stmt->fetch();

if (!$tugas) {
    die("Tugas tidak ditemukan.");
}

// Cek batas waktu
if ($tugas['batas_waktu'] && strtotime($tugas['batas_waktu']) < time()) {
    die("<p>Waktu pengumpulan telah berakhir.</p>");
}

// Proses upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file_tugas']) || $_FILES['file_tugas']['error'] !== 0) {
        $error = "Upload file gagal.";
    } else {
        $file = $_FILES['file_tugas'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nama_file = "tugas_" . $siswa_id . "_t" . $tugas_id . "_" . time() . "." . $ext;
        $path = "../uploads/tugas/" . $nama_file;

        if (move_uploaded_file($file['tmp_name'], $path)) {
            // Simpan ke database
            $stmt = $pdo->prepare("INSERT INTO pengumpulan_tugas (tugas_id, siswa_id, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$tugas_id, $siswa_id, $nama_file]);

            // Beri poin: 20 poin untuk kumpulkan tugas
            $stmt = $pdo->prepare("INSERT INTO poin (siswa_id, jenis, deskripsi, jumlah) VALUES (?, 'tugas', 'Tugas ID: $tugas_id', 20)");
            $stmt->execute([$siswa_id]);

            // Cek pencapaian lencana "Rajin Tugas"
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pengumpulan_tugas WHERE siswa_id = ?");
            $stmt->execute([$siswa_id]);
            $total_tugas = $stmt->fetchColumn();

            if ($total_tugas >= 5) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO lencana_siswa (siswa_id, lencana_id) VALUES (?, 2)");
                $stmt->execute([$siswa_id]);
            }

            echo "<script>alert('Tugas berhasil dikumpulkan!'); location.href='tugas.php';</script>";
            exit;
        } else {
            $error = "Gagal menyimpan file.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📤 Kumpulkan Tugas</h1>

    <div class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <h3 class="text-xl font-semibold"><?= htmlspecialchars($tugas['judul']) ?></h3>
        <p class="text-gray-600 mt-2">Unggah file tugas kamu di bawah ini.</p>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="mt-6">
            <label class="block text-gray-700 mb-2">File Tugas (PDF, DOCX, JPG, ZIP)</label>
            <input type="file" name="file_tugas" required class="w-full border border-gray-300 rounded-lg p-2">

            <div class="mt-6">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold transition">
                    📤 Submit Tugas
                </button>
                <a href="tugas.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>AOS.init();</script>
</body>

</html>