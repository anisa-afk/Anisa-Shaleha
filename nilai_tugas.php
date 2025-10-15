<!-- guru/nilai_tugas.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

// Ambil semua tugas yang dibuat guru
$stmt = $pdo->prepare("
    SELECT t.id, t.judul, t.batas_waktu 
    FROM tugas t 
    WHERE t.guru_id = ? 
    ORDER BY t.created_at DESC
");
$stmt->execute([$guru_id]);
$daftar_tugas = $stmt->fetchAll();

// Jika pilih tugas tertentu
$tugas_id = $_GET['tugas_id'] ?? null;
$submissions = [];

if ($tugas_id) {
    $stmt = $pdo->prepare("
        SELECT pt.*, s.nama, s.nis, s.kelas, u.email
        FROM pengumpulan_tugas pt
        JOIN siswa s ON pt.siswa_id = s.id
        JOIN users u ON s.user_id = u.id
        WHERE pt.tugas_id = ?
        ORDER BY pt.waktu_kumpul ASC
    ");
    $stmt->execute([$tugas_id]);
    $submissions = $stmt->fetchAll();
}

// Proses penilaian
// if method post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['aksi'] === 'nilai') {
        $id = $_POST['id'];
        $nilai = $_POST['nilai'];
        $komentar = $_POST['komentar'];

        $stmt = $pdo->prepare("UPDATE pengumpulan_tugas SET nilai = ?, komentar = ?, dikomentari = 1 WHERE id = ?");
        $stmt->execute([$nilai, $komentar, $id]);

        // Beri poin tambahan jika nilai bagus (opsional)
        if ($nilai >= 80) {
            $stmt = $pdo->prepare("SELECT siswa_id FROM pengumpulan_tugas WHERE id = ?");
            $stmt->execute([$id]);
            $siswa_id = $stmt->fetchColumn();

            $stmt = $pdo->prepare("INSERT INTO poin (siswa_id, jenis, deskripsi, jumlah) VALUES (?, 'hadiah', 'Nilai bagus: Tugas ID $tugas_id', 10)");
            $stmt->execute([$siswa_id]);
        }

        header("Location: nilai_tugas.php?tugas_id=$tugas_id&msg=success");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">✅ Nilai Tugas Siswa</h1>

    <!-- Pilih Tugas -->
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <label class="block text-gray-700 mb-2">Pilih Tugas</label>
        <select onchange="if(this.value) location.href='nilai_tugas.php?tugas_id='+this.value"
            class="border rounded-lg px-4 py-2 w-full md:w-1/2">
            <option value="">-- Pilih Tugas --</option>
            <?php foreach ($daftar_tugas as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $tugas_id == $t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['judul']) ?>
                    (<?= $t['batas_waktu'] ? date('d M', strtotime($t['batas_waktu'])) : 'No deadline' ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">Nilai berhasil disimpan.
        </div>
    <?php endif; ?>

    <?php if ($tugas_id && !$submissions): ?>
        <p class="text-gray-500">Belum ada siswa yang mengumpulkan tugas ini.</p>
    <?php elseif ($submissions): ?>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($submissions as $s): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium"><?= htmlspecialchars($s['nama']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($s['nis']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($s['kelas']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= date('d M H:i', strtotime($s['waktu_kumpul'])) ?></td>
                            <td class="px-6 py-4 text-sm">
                                <a href="../uploads/tugas/<?= htmlspecialchars($s['file_path']) ?>" target="_blank"
                                    class="text-blue-600 hover:underline">📄 Lihat</a>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($s['nilai']): ?>
                                    <span class="font-semibold text-green-600"><?= $s['nilai'] ?></span>
                                <?php else: ?>
                                    <form method="POST" class="space-y-2">
                                        <input type="hidden" name="aksi" value="nilai">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <input type="number" name="nilai" min="0" max="100" placeholder="Nilai" required
                                            class="border rounded px-2 py-1 w-20 text-sm">
                                        <input name="komentar" type="text" rows="1" placeholder="Komentar (opsional)"
                                            class="border rounded px-2 py-1 w-75 text-sm">
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">
                                            Simpan
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>