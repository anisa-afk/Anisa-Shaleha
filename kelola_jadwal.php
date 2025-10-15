<!-- admin/kelola_jadwal.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if ($_SESSION['role'] !== 'admin')
    exit;

$stmt = $pdo->prepare("SELECT * FROM jadwal ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jam_mulai");
$stmt->execute();
$jadwal_list = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <div class="flex justify-between mb-6">
        <h1 class="text-3xl font-bold">📅 Jadwal Pelajaran</h1>
        <a href="tambah_jadwal.php" class="bg-blue-600 text-white px-4 py-2 rounded">➕ Tambah Jadwal</a>
    </div>

    <table class="min-w-full bg-white border rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3">Hari</th>
                <th class="px-6 py-3">Jam</th>
                <th class="px-6 py-3">Mata Pelajaran</th>
                <th class="px-6 py-3">Kelas</th>
                <th class="px-6 py-3">Guru</th>
                <th class="px-6 py-3">Ruang</th>
                <th class="px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jadwal_list as $j): ?>
                <tr class="border-t">
                    <td class="px-6 py-4 font-medium"><?= $j['hari'] ?></td>
                    <td class="px-6 py-4"><?= date('H:i', strtotime($j['jam_mulai'])) ?> -
                        <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($j['mata_pelajaran']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($j['kelas']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($j['guru_nama']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($j['ruang']) ?></td>
                    <td class="px-6 py-4">
                        <a href="edit_jadwal.php?id=<?= $j['id'] ?>">✏️</a>
                        <a href="hapus_jadwal.php?id=<?= $j['id'] ?>" onclick="return confirm('Hapus?')"
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