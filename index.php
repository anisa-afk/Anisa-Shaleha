<!-- index.php (versi landing page) -->
<?php
// Redirect jika sudah login
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    $redirect = $role === 'guru' ? 'guru/dashboard.php' : 'siswa/dashboard.php';
    header("Location: $redirect");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Gamifikasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="text-center max-w-lg p-8 bg-white rounded-2xl shadow-lg" data-aos="zoom-in">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">🎓 E-Learning</h1>
        <p class="text-gray-600 mb-8">Platform pembelajaran interaktif dengan sistem gamifikasi: poin, lencana, dan
            leaderboard!</p>
        <div class="space-x-4">
            <a href="auth/login.php"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Masuk Sekarang
            </a>
        </div>
    </div>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000 });
    </script>
</body>

</html>