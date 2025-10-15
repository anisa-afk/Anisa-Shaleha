<!-- admin/tambah_guru.php -->
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
    $nuptk = $_POST['nuptk'] ?? '';
    $mata_pelajaran = $_POST['mata_pelajaran'] ?? '';
    $kelas_yang_diajar = $_POST['kelas_yang_diajar'] ?? '';
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

            // Insert ke users (role guru)
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'guru')");
            $stmt->execute([$email]);
            $user_id = $pdo->lastInsertId();

            // Insert ke guru
            $stmt = $pdo->prepare("INSERT INTO guru (user_id, nama, nuptk, mata_pelajaran, kelas_yang_diajar, telepon, alamat) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $nama, $nuptk, $mata_pelajaran, $kelas_yang_diajar, $telepon, $alamat]);

            $pdo->commit();
            $success = "Guru berhasil ditambahkan!";
        } catch (Exception $e) {
            $pdo->rollback();
            $error = "Gagal menambahkan guru: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">➕ Tambah Guru Baru</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
        <a href="kelola_guru.php" class="text-blue-600 hover:underline">← Kembali ke daftar guru</a>
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
                <label class="block text-gray-700 mb-2">NUPTK</label>
                <input type="text" name="nuptk" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['nuptk'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['mata_pelajaran'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Kelas yang Diajar</label>
                <input type="text" name="kelas_yang_diajar" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['kelas_yang_diajar'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Telepon</label>
                <input type="text" name="telepon" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Alamat</label>
                <input type="text" name="alamat" class="w-full border rounded-lg px-4 py-2"
                    value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                Simpan Guru
            </button>
            <a href="kelola_guru.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>