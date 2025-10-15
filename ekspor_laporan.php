<!-- guru/ekspor_laporan.php -->
<?php
// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
    die("Akses ditolak.");
}

$guru = get_guru_data($pdo);
if (!$guru) {
    die("Data guru tidak ditemukan.");
}
$guru_id = $guru['id'];

// Ambil parameter
$jenis = $_GET['jenis'] ?? '';
$tugas_id = $_GET['tugas_id'] ?? null;
$kuis_id = $_GET['kuis_id'] ?? null;

// Validasi input
if (!in_array($jenis, ['tugas', 'kuis', 'semua'])) {
    die("Jenis laporan tidak valid.");
}

$data = [];
$periode = "Semua";

// Query berdasarkan jenis
if ($jenis === 'tugas' || $jenis === 'semua') {
    $sql = "SELECT 
                t.judul as tugas, 
                s.nama as siswa, 
                pt.waktu_kumpul, 
                pt.nilai, 
                pt.komentar
            FROM pengumpulan_tugas pt
            JOIN tugas t ON pt.tugas_id = t.id
            JOIN siswa s ON pt.siswa_id = s.id
            WHERE t.guru_id = ?";
    $params = [$guru_id];

    if ($tugas_id) {
        $sql .= " AND t.id = ?";
        $params[] = $tugas_id;
        $stmt = $pdo->prepare("SELECT judul FROM tugas WHERE id = ?");
        $stmt->execute([$tugas_id]);
        $tugas = $stmt->fetch();
        $periode = "Tugas: " . $tugas['judul'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tugas_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tugas_data as $row) {
        $row['jenis'] = 'Tugas';
        $data[] = $row;
    }
}

if ($jenis === 'kuis' || $jenis === 'semua') {
    $sql = "SELECT 
                k.judul as kuis, 
                s.nama as siswa, 
                COUNT(js.id) as jumlah_jawaban, 
                SUM(js.is_benar) as benar, 
                ROUND((SUM(js.is_benar) * 100.0 / COUNT(js.id)), 1) as skor_persen,
                MAX(js.waktu_jawab) as waktu_selesai
            FROM jawaban_siswa js
            JOIN kuis k ON js.kuis_id = k.id
            JOIN siswa s ON js.siswa_id = s.id
            WHERE k.guru_id = ?";
    $params = [$guru_id];

    if ($kuis_id) {
        $sql .= " AND k.id = ?";
        $params[] = $kuis_id;
        $stmt = $pdo->prepare("SELECT judul FROM kuis WHERE id = ?");
        $stmt->execute([$kuis_id]);
        $kuis = $stmt->fetch();
        $periode = "Kuis: " . $kuis['judul'];
    }

    $sql .= " GROUP BY k.id, s.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $kuis_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($kuis_data as $row) {
        $row['jenis'] = 'Kuis';
        $data[] = $row;
    }
}

if (empty($data)) {
    die("Tidak ada data untuk diekspor.");
}

// Nama file
$filename = "Laporan_" . ucfirst($jenis) . "_" . date('Ymd_His') . ".xls";

// Header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo '<html>';
echo '<head><meta charset="UTF-8"><title>Laporan E-Learning</title></head>';
echo '<body>';
echo '<h2>LAPORAN ' . strtoupper($jenis) . '</h2>';
echo '<p><strong>Periode:</strong> ' . $periode . '</p>';
echo '<p><strong>Waktu Ekspor:</strong> ' . date('d-m-Y H:i:s') . '</p>';
echo '<p><strong>Guru:</strong> ' . htmlspecialchars($guru['nama']) . '</p>';

echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<thead>';
echo '<tr style="background-color: #003366; color: white; font-weight: bold;">';

if ($jenis === 'tugas' || $jenis === 'semua') {
    echo '<th>Jenis</th><th>Tugas</th><th>Siswa</th><th>Tanggal Kumpul</th><th>Nilai</th><th>Komentar</th>';
} else {
    echo '<th>Jenis</th><th>Kuis</th><th>Siswa</th><th>Jawaban Benar</th><th>Skor (%)</th><th>Waktu Selesai</th>';
}

echo '</tr></thead><tbody>';

foreach ($data as $row) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['jenis']) . '</td>';

    if ($row['jenis'] === 'Tugas') {
        echo '<td>' . htmlspecialchars($row['tugas']) . '</td>';
        echo '<td>' . htmlspecialchars($row['siswa']) . '</td>';
        echo '<td>' . date('d-m-Y H:i', strtotime($row['waktu_kumpul'])) . '</td>';
        echo '<td>' . ($row['nilai'] ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row['komentar'] ?? '-') . '</td>';
    } else {
        echo '<td>' . htmlspecialchars($row['kuis']) . '</td>';
        echo '<td>' . htmlspecialchars($row['siswa']) . '</td>';
        echo '<td>' . $row['benar'] . ' / ' . $row['jumlah_jawaban'] . '</td>';
        echo '<td>' . $row['skor_persen'] . '%</td>';
        echo '<td>' . date('d-m-Y H:i', strtotime($row['waktu_selesai'])) . '</td>';
    }
    echo '</tr>';
}

echo '</tbody></table></body></html>';

exit;