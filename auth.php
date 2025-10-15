<!-- includes/auth.php -->
<?php
session_start();

function login($email, $password, $pdo)
{
    $stmt = $pdo->prepare("
        SELECT u.id, u.email, u.password, u.role, 
               g.nama AS nama_guru, s.nama AS nama_siswa
        FROM users u
        LEFT JOIN guru g ON u.id = g.user_id
        LEFT JOIN siswa s ON u.id = s.user_id
        WHERE u.email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['role'] === 'guru' ? $user['nama_guru'] : $user['nama_siswa'];
        return true;
    }
    return false;
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function redirect_if_not_logged_in()
{
    if (!is_logged_in()) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function get_siswa_data($pdo)
{
    if ($_SESSION['role'] !== 'siswa')
        return null;
    $stmt = $pdo->prepare("SELECT * FROM siswa WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function get_guru_data($pdo)
{
    if ($_SESSION['role'] !== 'guru')
        return null;
    $stmt = $pdo->prepare("SELECT * FROM guru WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function get_user_poin($siswa_id, $pdo)
{
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah), 0) AS total FROM poin WHERE siswa_id = ?");
    $stmt->execute([$siswa_id]);
    return $stmt->fetch()['total'];
}

function get_user_level($siswa_id, $pdo)
{
    $poin = get_user_poin($siswa_id, $pdo);
    $stmt = $pdo->prepare("SELECT nama FROM level WHERE poin_syarat <= ? ORDER BY poin_syarat DESC LIMIT 1");
    $stmt->execute([$poin]);
    return $stmt->fetch()['nama'] ?? 'Pemula';
}
?>