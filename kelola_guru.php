<!-- admin/kelola_guru.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if ($_SESSION['role'] !== 'admin')
    exit;

$stmt = $pdo->prepare("
    SELECT g.*, u.email 
    FROM guru g 
    JOIN users u ON g.user_id = u.id 
    ORDER BY g.nama
");
$stmt->execute();
$guru_list = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <div class="flex justify-between mb-6">
        <h1 class="text-3xl font-bold">🧑‍🏫 Kelola Guru</h1>
        <a href="tambah_guru.php" class="bg-blue-600 text-white px-4 py-2 rounded">➕ Tambah Guru</a>
    </div>

    <table class="min-w-full bg-white border rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-left">NUPTK</th>
                <th class="px-6 py-3 text-left">Mata Pelajaran</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($guru_list as $g): ?>
                <tr class="border-t">
                    <td class="px-6 py-4"><?= htmlspecialchars($g['nama']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($g['nuptk']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($g['mata_pelajaran']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($g['email']) ?></td>
                    <td class="px-6 py-4">
                        <a href="edit_guru.php?id=<?= $g['id'] ?>" class="text-blue-600">✏️</a>
                        <a href="hapus_guru.php?id=<?= $g['id'] ?>" onclick="return confirm('Hapus guru ini?')"
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