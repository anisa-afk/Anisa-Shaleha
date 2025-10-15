<!-- auth/login.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password, $pdo)) {
        $role = $_SESSION['role'];

        if ($role === 'guru') {
            $redirect = '../guru/dashboard.php';
        } elseif ($role === 'siswa') {
            $redirect = '../siswa/dashboard.php';
        } elseif ($role === 'admin') {
            $redirect = '../admin/dashboard.php';
        } else {
            $redirect = '../index.php'; // fallback
        }

        header("Location: $redirect");
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100"
    data-aos="zoom-in">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md" style="border: 1px solid #e2e8f0;">
        <h1 class="text-2xl font-bold text-center text-blue-700 mb-6">🔐 Login E-Learning</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="guru@sekolah.com / siswa@sekolah.com">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-gray-500 text-xs mt-4">
            Demo: Gunakan email & password apa pun (asumsi sudah ada data dummy)
        </p>
    </div>
</div>

<script>
    AOS.init({ duration: 1000, easing: 'ease-in-out' });
</script>
</body>

</html>