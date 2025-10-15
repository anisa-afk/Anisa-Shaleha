<!-- admin/edit_siswa.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kelola_siswa.php");
    exit;
}

$id = $_GET['id'];

// Ambil data siswa + user
$stmt = $pdo->prepare("
    SELECT s.*, u.email 
    FROM siswa s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.id = ?
");
$stmt->execute([$id]);
$siswa = $stmt->fetch();

if (!$siswa) {
    die("Siswa tidak ditemukan.");
}

$error = '';
$success = '';

if ($_POST) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $nis = $_POST['nis'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $angkatan = $_POST['angkatan'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    if (empty($nama) || empty($email)) {
        $error = "Nama dan email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        try {
            $pdo->beginTransaction();

            // Update users.email
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $siswa['user_id']]);

            // Update siswa
            $stmt = $pdo->prepare("UPDATE siswa SET nama = ?, nis = ?, kelas = ?, angkatan = ?, jenis_kelamin = ?, telepon = ?, alamat = ? WHERE id = ?");
            $stmt->execute([$nama, $nis, $kelas, $angkatan, $jenis_kelamin, $telepon, $alamat, $id]);

            $pdo->commit();
            $success = "Data siswa berhasil diperbarui!";
        } catch (Exception $e) {
            $pdo->rollback();
            $error = "Gagal memperbarui siswa: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ Edit Siswa</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
        <a href="kelola_siswa.php" class="text-blue-600 hover:underline">← Kembali</a>
        <?php exit; ?>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['nama']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['email']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">NIS</label>
                <input type="text" name="nis" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['nis']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Kelas</label>
                <input type="text" name="kelas" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['kelas']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Angkatan</label>
                <input type="number" name="angkatan" min="2020" max="2030" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['angkatan']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border rounded-lg px-4 py-2">
                    <option value="">-- Pilih --</option>
                    <option value="L" <?= ($siswa['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= ($siswa['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Telepon</label>
                <input type="text" name="telepon" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['telepon']) ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2">Alamat</label>
                <input type="text" name="alamat" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($siswa['alamat']) ?>">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">
                Perbarui
            </button>
            <a href="kelola_siswa.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>