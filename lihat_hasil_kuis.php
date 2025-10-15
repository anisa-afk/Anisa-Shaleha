<!-- guru/lihat_hasil_kuis.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
$guru_id = $guru['id'];

// Ambil daftar kuis milik guru
$stmt = $pdo->prepare("SELECT id, judul FROM kuis WHERE guru_id = ? ORDER BY created_at DESC");
$stmt->execute([$guru_id]);
$daftar_kuis = $stmt->fetchAll();

$kuis_id = $_GET['kuis_id'] ?? null;
$hasil = [];

if ($kuis_id) {
    // Cek apakah kuis ini milik guru
    $stmt = $pdo->prepare("SELECT id FROM kuis WHERE id = ? AND guru_id = ?");
    $stmt->execute([$kuis_id, $guru_id]);
    if (!$stmt->fetch()) {
        die("Kuis tidak ditemukan atau bukan milik Anda.");
    }

    // Ambil hasil kuis per siswa
    $stmt = $pdo->prepare("
        SELECT 
            s.nama, s.nis, s.kelas,
            COUNT(js.id) AS jumlah_jawaban,
            SUM(js.is_benar) AS benar,
            (SUM(js.is_benar) * 100.0 / COUNT(js.id)) AS skor_persen
        FROM siswa s
        JOIN jawaban_siswa js ON s.id = js.siswa_id
        WHERE js.kuis_id = ?
        GROUP BY s.id
        ORDER BY skor_persen DESC
    ");
    $stmt->execute([$kuis_id]);
    $hasil = $stmt->fetchAll();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Hasil Kuis Siswa</h1>

    <!-- Pilih Kuis -->
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <label class="block text-gray-700 mb-2">Pilih Kuis</label>
        <select onchange="if(this.value) location.href='lihat_hasil_kuis.php?kuis_id='+this.value"
            class="border rounded-lg px-4 py-2 w-full md:w-1/2">
            <option value="">-- Pilih Kuis --</option>
            <?php foreach ($daftar_kuis as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $kuis_id == $k['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['judul']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($kuis_id && !$hasil): ?>
        <p class="text-gray-500">Belum ada siswa yang mengerjakan kuis ini.</p>
    <?php elseif ($hasil): ?>
        <h2 class="text-xl font-semibold mb-4">📝 Hasil untuk:
            <?php
            $stmt = $pdo->prepare("SELECT judul FROM kuis WHERE id = ?");
            $stmt->execute([$kuis_id]);
            echo htmlspecialchars($stmt->fetchColumn());
            ?>
        </h2>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Benar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($hasil as $h): ?>
                        <tr>
                            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($h['nama']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($h['nis']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($h['kelas']) ?></td>
                            <td class="px-6 py-4 text-green-600 font-semibold"><?= $h['benar'] ?></td>
                            <td class="px-6 py-4"><?= $h['jumlah_jawaban'] ?></td>
                            <td
                                class="px-6 py-4 font-semibold <?= $h['skor_persen'] >= 75 ? 'text-green-600' : 'text-orange-600' ?>">
                                <?= number_format($h['skor_persen'], 1) ?>%
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