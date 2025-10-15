<!-- siswa/jadwal.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold mb-6">📅 Jadwal Pelajaran</h1>
    <p class="text-gray-600 mb-6">Jadwal untuk semua kelas.</p>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM jadwal ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jam_mulai");
    $stmt->execute();
    $jadwal = $stmt->fetchAll();
    ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded-lg">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-3">Hari</th>
                    <th class="px-6 py-3">Jam</th>
                    <th class="px-6 py-3">Mata Pelajaran</th>
                    <th class="px-6 py-3">Kelas</th>
                    <th class="px-6 py-3">Guru</th>
                    <th class="px-6 py-3">Ruang</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jadwal as $j): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium"><?= $j['hari'] ?></td>
                        <td class="px-6 py-4"><?= date('H:i', strtotime($j['jam_mulai'])) ?> -
                            <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($j['mata_pelajaran']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($j['kelas']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($j['guru_nama']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($j['ruang']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>AOS.init();</script>
</body>

</html>