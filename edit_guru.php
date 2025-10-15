<!-- admin/edit_guru.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kelola_guru.php");
    exit;
}

$id = $_GET['id'];

// Ambil data guru + user
$stmt = $pdo->prepare("
    SELECT g.*, u.email 
    FROM guru g 
    JOIN users u ON g.user_id = u.id 
    WHERE g.id = ?
");
$stmt->execute([$id]);
$guru = $stmt->fetch();

if (!$guru) {
    die("Guru tidak ditemukan.");
}

$error = '';
$success = '';

if ($_POST) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $nuptk = $_POST['nuptk'] ?? '';
    $mata_pelajaran = $_POST['mata_pelajaran'] ?? '';
    $kelas_yang_diajar = $_POST['kelas_yang_diajar'] ?? '';
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
            $stmt->execute([$email, $guru['user_id']]);

            // Update guru
            $stmt = $pdo->prepare("UPDATE guru SET nama = ?, nuptk = ?, mata_pelajaran = ?, kelas_yang_diajar = ?, telepon = ?, alamat = ? WHERE id = ?");
            $stmt->execute([$nama, $nuptk, $mata_pelajaran, $kelas_yang_diajar, $telepon, $alamat, $id]);

            $pdo->commit();
            $success = "Data guru berhasil diperbarui!";
        } catch (Exception $e) {
            $pdo->rollback();
            $error = "Gagal memperbarui guru: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ Edit Guru</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
        <a href="kelola_guru.php" class="text-blue-600 hover:underline">← Kembali</a>
        <?php exit; ?>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['nama']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['email']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">NUPTK</label>
                <input type="text" name="nuptk" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['nuptk']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['mata_pelajaran']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Kelas yang Diajar</label>
                <input type="text" name="kelas_yang_diajar" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['kelas_yang_diajar']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Telepon</label>
                <input type="text" name="telepon" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['telepon']) ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2">Alamat</label>
                <input type="text" name="alamat" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($guru['alamat']) ?>">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">
                Perbarui
            </button>
            <a href="kelola_guru.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>