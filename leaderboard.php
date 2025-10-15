<!-- siswa/leaderboard.php -->
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirect_if_not_logged_in();

// Ambil peringkat siswa
$stmt = $pdo->prepare("
    SELECT s.nama, s.kelas, 
           COALESCE(SUM(p.jumlah), 0) AS total_poin, s.id   
    FROM siswa s
    LEFT JOIN poin p ON s.id = p.siswa_id
    GROUP BY s.id
    ORDER BY total_poin DESC
    LIMIT 20
");
$stmt->execute();
$leaderboard = $stmt->fetchAll();

$my_rank = array_search($_SESSION['user_id'], array_column($leaderboard, 'id')) + 1;
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="ml-64 p-6 flex-1">
    <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">🏆 Leaderboard (Peringkat)</h1>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poin</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($leaderboard as $index => $s): ?>
                    <tr class="<?= $s['id'] == $_SESSION['user_id'] ? 'bg-blue-50' : '' ?>">
                        <td class="px-6 py-4 text-sm font-medium"><?= $index + 1 ?></td>
                        <td class="px-6 py-4 text-sm"><?= htmlspecialchars($s['nama']) ?></td>
                        <td class="px-6 py-4 text-sm"><?= htmlspecialchars($s['kelas']) ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-green-600"><?= $s['total_poin'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="mt-6 text-gray-600">
        Kamu berada di peringkat ke-<strong>
            <?php
            $my_pos = null;
            foreach ($leaderboard as $i => $s) {
                if ($s['id'] == get_siswa_data($pdo)['id']) {
                    $my_pos = $i + 1;
                    break;
                }
            }
            echo $my_pos ?? '?';
            ?>
        </strong>
    </p>
</div>

<script>AOS.init();</script>
</body>

</html>