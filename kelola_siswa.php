<!-- admin/kelola_siswa.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if ($_SESSION['role'] !== 'admin')
    exit;

$stmt = $pdo->prepare("
    SELECT s.*, u.email 
    FROM siswa s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.nama
");
$stmt->execute();
$siswa_list = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <div class="flex justify-between mb-6">
        <h1 class="text-3xl font-bold">🧑‍🎓 Kelola Siswa</h1>
        <a href="tambah_siswa.php" class="bg-blue-600 text-white px-4 py-2 rounded">➕ Tambah Siswa</a>
    </div>

    <table class="min-w-full bg-white border rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-left">NIS</th>
                <th class="px-6 py-3 text-left">Kelas</th>
                <th class="px-6 py-3 text-left">Angkatan</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($siswa_list as $s): ?>
                <tr class="border-t">
                    <td class="px-6 py-4"><?= htmlspecialchars($s['nama']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($s['nis']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($s['kelas']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($s['angkatan']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($s['email']) ?></td>
                    <td class="px-6 py-4">
                        <a href="edit_siswa.php?id=<?= $s['id'] ?>" class="text-blue-600">✏️</a>
                        <a href="hapus_siswa.php?id=<?= $s['id'] ?>" onclick="return confirm('Hapus siswa ini?')"
                            class="text-red-600 ml-2">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>AOS.init();</script>
</body>

</html>