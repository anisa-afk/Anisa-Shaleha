<!-- admin/tambah_siswa.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nis = $_POST['nis'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $angkatan = $_POST['angkatan'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $telepon = $_POST['telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    // Validasi
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Nama, email, dan password wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        try {
            $pdo->beginTransaction();

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke users (role siswa)
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'siswa')");
            $stmt->execute([$email]);
            $user_id = $pdo->lastInsertId();

            // Insert ke siswa
            $stmt = $pdo->prepare("INSERT INTO siswa (user_id, nama, nis, kelas, angkatan, jenis_kelamin, telepon, alamat) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $nama, $nis, $kelas, $angkatan, $jenis_kelamin, $telepon, $alamat]);

            $pdo->commit();
            $success = "Siswa berhasil ditambahkan!";
        } catch (Exception $e) {
            $pdo->rollback();
            $error = "Gagal menambahkan siswa: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">➕ Tambah Siswa Baru</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
        <a href="kelola_siswa.php" class="text-blue-600 hover:underline">← Kembali ke daftar siswa</a>
        <?php exit; ?>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required class="w-full border rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">NIS</label>
                <input type="text" name="nis" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Kelas</label>
                <input type="text" name="kelas" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['kelas'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Angkatan</label>
                <input type="number" name="angkatan" min="2020" max="2030" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['angkatan'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border rounded-lg px-4 py-2">
                    <option value="">-- Pilih --</option>
                    <option value="L" <?= (($_POST['jenis_kelamin'] ?? '') == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= (($_POST['jenis_kelamin'] ?? '') == 'P') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Telepon</label>
                <input type="text" name="telepon" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2">Alamat</label>
                <input type="text" name="alamat" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                Simpan Siswa
            </button>
            <a href="kelola_siswa.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>