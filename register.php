<!-- auth/register.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$error = '';
$success = '';

if ($_POST) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validasi input
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        // Cek apakah email sudah ada
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email sudah terdaftar.";
        } else {
            try {
                $pdo->beginTransaction();

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert ke users
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hashed_password, $role]);
                $user_id = $pdo->lastInsertId();

                // Insert ke tabel siswa atau guru
                if ($role === 'siswa') {
                    $nis = $_POST['nis'] ?? '';
                    $kelas = $_POST['kelas'] ?? '';
                    $angkatan = $_POST['angkatan'] ?? null;

                    $stmt = $pdo->prepare("INSERT INTO siswa (user_id, nama, nis, kelas, angkatan) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $nama, $nis, $kelas, $angkatan]);
                } elseif ($role === 'guru') {
                    $nuptk = $_POST['nuptk'] ?? '';
                    $mata_pelajaran = $_POST['mata_pelajaran'] ?? '';

                    $stmt = $pdo->prepare("INSERT INTO guru (user_id, nama, nuptk, mata_pelajaran) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $nama, $nuptk, $mata_pelajaran]);
                }

                $pdo->commit();
                $success = "Registrasi berhasil! Silakan login.";
            } catch (Exception $e) {
                $pdo->rollback();
                $error = "Terjadi kesalahan saat registrasi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-blue-700 mb-6">📝 Daftar Akun</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                <input type="text" name="nama" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Nama lengkap Anda">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="email@sekolah.com">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Minimal 6 karakter">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ulangi password">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Daftar Sebagai</label>
                <select name="role" required onchange="toggleFields(this.value)"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Peran --</option>
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                </select>
            </div>

            <!-- Field untuk Siswa -->
            <div id="siswa-fields" class="space-y-4 mb-4 hidden">
                <div>
                    <label class="block text-gray-700 text-sm font-medium">NIS</label>
                    <input type="text" name="nis" class="w-full px-4 py-2 border rounded-lg"
                        placeholder="Nomor Induk Siswa">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium">Kelas</label>
                    <input type="text" name="kelas" class="w-full px-4 py-2 border rounded-lg"
                        placeholder="Contoh: X IPA 1">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium">Angkatan</label>
                    <input type="number" name="angkatan" min="2020" max="2030"
                        class="w-full px-4 py-2 border rounded-lg" placeholder="2024">
                </div>
            </div>

            <!-- Field untuk Guru -->
            <div id="guru-fields" class="space-y-4 mb-4 hidden">
                <div>
                    <label class="block text-gray-700 text-sm font-medium">NUPTK</label>
                    <input type="text" name="nuptk" class="w-full px-4 py-2 border rounded-lg"
                        placeholder="Nomor Unik Pendidik">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium">Mata Pelajaran</label>
                    <input type="text" name="mata_pelajaran" class="w-full px-4 py-2 border rounded-lg"
                        placeholder="Contoh: Matematika">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Daftar Akun
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-4">
            Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Masuk di sini</a>
        </p>
    </div>

    <script>
        function toggleFields(role) {
            document.getElementById('siswa-fields').style.display = role === 'siswa' ? 'block' : 'none';
            document.getElementById('guru-fields').style.display = role === 'guru' ? 'block' : 'none';
        }
    </script>
</body>

</html>