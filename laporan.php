<!-- guru/laporan.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

$guru = get_guru_data($pdo);
if (!$guru) {
    die("Data guru tidak ditemukan.");
}
$guru_id = $guru['id'];
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Pilih Laporan untuk Ekspor Excel</h1>

    <form action="ekspor_laporan.php" method="GET" class="bg-white p-6 rounded-xl shadow max-w-2xl">
        <!-- Jenis Laporan -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Jenis Laporan</label>
            <select name="jenis" required onchange="toggleDetail(this.value)"
                class="w-full border rounded-lg px-4 py-2">
                <option value="">-- Pilih Jenis --</option>
                <option value="tugas">Tugas</option>
                <option value="kuis">Kuis</option>
                <option value="semua">Semua (Tugas & Kuis)</option>
            </select>
        </div>

        <!-- Pilih Tugas -->
        <div id="tugas-section" class="mb-4 hidden">
            <label class="block text-gray-700 mb-2">Pilih Tugas</label>
            <select name="tugas_id" class="w-full border rounded-lg px-4 py-2">
                <option value="">Semua tugas</option>
                <?php
                $stmt = $pdo->prepare("SELECT id, judul FROM tugas WHERE guru_id = ? ORDER BY created_at DESC");
                $stmt->execute([$guru_id]);
                $tugas_list = $stmt->fetchAll();
                foreach ($tugas_list as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['judul']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Pilih Kuis -->
        <div id="kuis-section" class="mb-4 hidden">
            <label class="block text-gray-700 mb-2">Pilih Kuis</label>
            <select name="kuis_id" class="w-full border rounded-lg px-4 py-2">
                <option value="">Semua kuis</option>
                <?php
                $stmt = $pdo->prepare("SELECT id, judul FROM kuis WHERE guru_id = ? ORDER BY created_at DESC");
                $stmt->execute([$guru_id]);
                $kuis_list = $stmt->fetchAll();
                foreach ($kuis_list as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['judul']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">
            📥 Ekspor ke Excel
        </button>
    </form>
</div>

<script>
    function toggleDetail(jenis) {
        document.getElementById('tugas-section').style.display = (jenis === 'tugas' || jenis === 'semua') ? 'block' : 'none';
        document.getElementById('kuis-section').style.display = (jenis === 'kuis' || jenis === 'semua') ? 'block' : 'none';
    }
</script>

<script>AOS.init();</script>
</body>

</html>