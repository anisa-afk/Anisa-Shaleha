<!-- guru/kelola_soal.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

if (!isset($_GET['kuis_id'])) {
    header("Location: kelola_kuis.php");
    exit;
}

$kuis_id = $_GET['kuis_id'];
$guru = get_guru_data($pdo);

// Cek apakah kuis milik guru
$stmt = $pdo->prepare("SELECT * FROM kuis WHERE id = ? AND guru_id = ?");
$stmt->execute([$kuis_id, $guru['id']]);
$kuis = $stmt->fetch();

if (!$kuis) {
    die("Kuis tidak ditemukan atau bukan milik Anda.");
}

// Ambil soal yang sudah ada
$stmt = $pdo->prepare("SELECT * FROM soal WHERE kuis_id = ? ORDER BY id");
$stmt->execute([$kuis_id]);
$soal_list = $stmt->fetchAll();

// Proses form jika disubmit
if (isset($_POST['action']) === 'tambah_banyak') {
    $jumlah = (int) $_POST['jumlah_soal'];
    $inputs = [];

    for ($i = 1; $i <= $jumlah; $i++) {
        $pertanyaan = trim($_POST["pertanyaan_$i"]);
        $a = trim($_POST["pilihan_a_$i"]);
        $b = trim($_POST["pilihan_b_$i"]);
        $c = trim($_POST["pilihan_c_$i"]);
        $d = trim($_POST["pilihan_d_$i"]);
        $jawaban = $_POST["jawaban_benar_$i"] ?? '';

        if (empty($pertanyaan) || empty($a) || empty($b) || empty($c) || empty($d) || empty($jawaban)) {
            die("Semua field soal ke-$i wajib diisi.");
        }

        $inputs[] = [$kuis_id, $pertanyaan, $a, $b, $c, $d, $jawaban];
    }

    // Simpan semua soal
    $stmt = $pdo->prepare("INSERT INTO soal (kuis_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($inputs as $input) {
        $stmt->execute($input);
    }

    header("Location: kelola_soal.php?kuis_id=$kuis_id&msg=tambah");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">📝 Kelola Soal: <?= htmlspecialchars($kuis['judul']) ?></h1>
    <p class="text-gray-600 mb-6">Tambahkan soal pilihan ganda dalam jumlah banyak sekaligus.</p>

    <!-- Form Input Jumlah Soal -->
    <?php if (!isset($_POST['jumlah_soal']) && !isset($_GET['action'])): ?>
        <div class="bg-white p-6 rounded-xl shadow mb-8 max-w-md">
            <form method="POST">
                <input type="hidden" name="action" value="tampilkan_form">
                <label class="block text-gray-700 mb-2">Berapa soal yang ingin ditambahkan?</label>
                <input type="number" name="jumlah_soal" min="1" max="50" required
                    class="w-full border rounded-lg px-4 py-2 mb-4" placeholder="Contoh: 5">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
                    Buat Form Soal
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Form Dinamis - Dibuat setelah input jumlah -->
    <?php if (isset($_POST['jumlah_soal']) && $_POST['action'] === 'tampilkan_form'): ?>
        <form method="POST" class="bg-white p-6 rounded-xl shadow mb-8">
            <input type="hidden" name="action" value="tambah_banyak">
            <input type="hidden" name="jumlah_soal" value="<?= (int) $_POST['jumlah_soal'] ?>">

            <h3 class="text-lg font-semibold mb-4">Isi Soal</h3>
            <?php for ($i = 1; $i <= (int) $_POST['jumlah_soal']; $i++): ?>
                <div class="border-t pt-6 mt-6 <?= $i === 1 ? 'border-t-0 pt-0 mt-0' : '' ?>">
                    <h4 class="font-medium text-gray-800">Soal <?= $i ?></h4>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Pertanyaan</label>
                        <textarea name="pertanyaan_<?= $i ?>" rows="2" required
                            class="w-full border rounded-lg px-4 py-2"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <input type="text" name="pilihan_a_<?= $i ?>" placeholder="Pilihan A" required
                            class="border rounded-lg px-4 py-2">
                        <input type="text" name="pilihan_b_<?= $i ?>" placeholder="Pilihan B" required
                            class="border rounded-lg px-4 py-2">
                        <input type="text" name="pilihan_c_<?= $i ?>" placeholder="Pilihan C" required
                            class="border rounded-lg px-4 py-2">
                        <input type="text" name="pilihan_d_<?= $i ?>" placeholder="Pilihan D" required
                            class="border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Jawaban Benar</label>
                        <select name="jawaban_benar_<?= $i ?>" required class="border rounded-lg px-4 py-2">
                            <option value="">-- Pilih --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>
            <?php endfor; ?>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">
                    📤 Simpan Semua Soal
                </button>
                <a href="kelola_soal.php?kuis_id=<?= $kuis_id ?>" class="ml-4 text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    <?php endif; ?>

    <!-- Daftar Soal yang Sudah Ada -->
    <h2 class="text-xl font-semibold mb-4">Daftar Soal</h2>
    <?php if ($soal_list): ?>
        <div class="space-y-4">
            <?php foreach ($soal_list as $s): ?>
                <div class="bg-gray-50 p-4 rounded border">
                    <p class="font-medium"><?= htmlspecialchars($s['pertanyaan']) ?></p>
                    <div class="text-sm text-gray-700 mt-2">
                        A: <?= htmlspecialchars($s['pilihan_a']) ?> |
                        B: <?= htmlspecialchars($s['pilihan_b']) ?> |
                        C: <?= htmlspecialchars($s['pilihan_c']) ?> |
                        D: <?= htmlspecialchars($s['pilihan_d']) ?>
                    </div>
                    <p class="text-sm text-green-600 mt-1">Jawaban Benar: <strong><?= $s['jawaban_benar'] ?></strong></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada soal.</p>
    <?php endif; ?>
</div>

<script>AOS.init();</script>
</body>

</html>