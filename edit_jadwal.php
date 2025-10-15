<!-- admin/edit_jadwal.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: kelola_jadwal.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM jadwal WHERE id = ?");
$stmt->execute([$id]);
$jadwal = $stmt->fetch();

if (!$jadwal) {
    die("Jadwal tidak ditemukan.");
}

$error = '';
$success = '';

if ($_POST) {
    $mata_pelajaran = trim($_POST['mata_pelajaran']);
    $kelas = trim($_POST['kelas']);
    $guru_nama = trim($_POST['guru_nama']);
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $ruang = $_POST['ruang'] ?? '';

    if (empty($mata_pelajaran) || empty($kelas) || empty($guru_nama) || empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        $error = "Semua field wajib diisi.";
    } else {
        $stmt = $pdo->prepare("UPDATE jadwal SET mata_pelajaran = ?, kelas = ?, guru_nama = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, ruang = ? WHERE id = ?");
        try {
            $stmt->execute([$mata_pelajaran, $kelas, $guru_nama, $hari, $jam_mulai, $jam_selesai, $ruang, $id]);
            $success = "Jadwal berhasil diperbarui!";
        } catch (Exception $e) {
            $error = "Gagal memperbarui jadwal.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ Edit Jadwal</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
        <a href="kelola_jadwal.php" class="text-blue-600 hover:underline">← Kembali</a>
        <?php exit; ?>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($jadwal['mata_pelajaran']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Kelas</label>
                <input type="text" name="kelas" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($jadwal['kelas']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Nama Guru</label>
                <input type="text" name="guru_nama" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($jadwal['guru_nama']) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Hari</label>
                <select name="hari" required class="w-full border rounded-lg px-4 py-2">
                    <option value="">-- Pilih Hari --</option>
                    <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h): ?>
                        <option value="<?= $h ?>" <?= $jadwal['hari'] === $h ? 'selected' : '' ?>><?= $h ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Jam Mulai</label>
                <input type="time" name="jam_mulai" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= substr($jadwal['jam_mulai'], 0, 5) ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Jam Selesai</label>
                <input type="time" name="jam_selesai" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= substr($jadwal['jam_selesai'], 0, 5) ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2">Ruang (Opsional)</label>
                <input type="text" name="ruang" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($jadwal['ruang']) ?>">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">
                Perbarui
            </button>
            <a href="kelola_jadwal.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>