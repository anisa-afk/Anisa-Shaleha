<!-- siswa/kerjakan_kuis.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

if (!isset($_GET['id'])) {
    header("Location: kuis.php");
    exit;
}

$kuis_id = $_GET['id'];
$siswa_id = get_siswa_data($pdo)['id'];

// Cek apakah sudah mengerjakan
$stmt = $pdo->prepare("SELECT COUNT(*) FROM jawaban_siswa WHERE siswa_id = ? AND kuis_id = ?");
$stmt->execute([$siswa_id, $kuis_id]);
if ($stmt->fetchColumn() > 0) {
    die("<p>Kamu sudah menyelesaikan kuis ini.</p>");
}

// Ambil soal
$stmt = $pdo->prepare("SELECT * FROM soal WHERE kuis_id = ?");
$stmt->execute([$kuis_id]);
$soal_list = $stmt->fetchAll();

if (!$soal_list) {
    die("<p>Belum ada soal untuk kuis ini.</p>");
}

// Saat submit
if ($_POST) {
    $benar = 0;
    foreach ($soal_list as $s) {
        $jawaban = $_POST['soal_' . $s['id']] ?? '';
        $is_benar = $jawaban === $s['jawaban_benar'];

        if ($is_benar)
            $benar++;

        $stmt = $pdo->prepare("INSERT INTO jawaban_siswa (siswa_id, soal_id, jawaban_pilihan, is_benar, kuis_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$siswa_id, $s['id'], $jawaban, $is_benar, $kuis_id]);
    }

    // Tambah poin: 10 poin per soal benar
    $poin_earned = $benar * 10;
    $stmt = $pdo->prepare("INSERT INTO poin (siswa_id, jenis, deskripsi, jumlah) VALUES (?, 'kuis', 'Kuis ID: $kuis_id', ?)");
    $stmt->execute([$siswa_id, $poin_earned]);

    // Cek pencapaian lencana (contoh: 5 kuis selesai)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT kuis_id) FROM jawaban_siswa WHERE siswa_id = ?");
    $stmt->execute([$siswa_id]);
    $total_kuis = $stmt->fetchColumn();

    if ($total_kuis >= 5) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO lencana_siswa (siswa_id, lencana_id) VALUES (?, 1)");
        $stmt->execute([$siswa_id]);
    }

    echo "<script>alert('Kuis selesai! Skor: $benar / " . count($soal_list) . ". Poin diberikan: $poin_earned'); location.href='kuis.php';</script>";
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📝 Kerjakan Kuis</h1>
    <form method="POST">
        <?php foreach ($soal_list as $index => $s): ?>
            <div class="bg-white p-6 rounded-xl shadow mb-6">
                <h3 class="font-semibold text-lg"><?= $index + 1 ?>. <?= htmlspecialchars($s['pertanyaan']) ?></h3>
                <div class="mt-3 space-y-2">
                    <label><input type="radio" name="soal_<?= $s['id'] ?>" value="A" required> A.
                        <?= htmlspecialchars($s['pilihan_a']) ?></label><br>
                    <label><input type="radio" name="soal_<?= $s['id'] ?>" value="B"> B.
                        <?= htmlspecialchars($s['pilihan_b']) ?></label><br>
                    <label><input type="radio" name="soal_<?= $s['id'] ?>" value="C"> C.
                        <?= htmlspecialchars($s['pilihan_c']) ?></label><br>
                    <label><input type="radio" name="soal_<?= $s['id'] ?>" value="D"> D.
                        <?= htmlspecialchars($s['pilihan_d']) ?></label>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
            📤 Submit Jawaban
        </button>
    </form>
</div>

<script>AOS.init();</script>
</body>

</html>