<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/remember.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Latihan 10.3: batas percobaan gagal (disimpan di session, per username)
const MAKS_GAGAL = 5;       // setelah 5x gagal berturut-turut...
const DURASI_KUNCI = 60;    // ...login untuk username itu dikunci 60 detik
const PERINGATAN_MULAI = 3; // peringatan sisa percobaan muncul mulai gagal ke-3

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$kunci = strtolower($username);

$gagal = $_SESSION['login_gagal'][$kunci] ?? ['jumlah' => 0, 'kunci_sampai' => 0];

// Sedang terkunci? Tolak tanpa memeriksa password sama sekali.
if ($gagal['kunci_sampai'] > time()) {
    $sisa = $gagal['kunci_sampai'] - time();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Terlalu banyak percobaan gagal. Coba lagi dalam $sisa detik."];
    header('Location: login.php');
    exit;
}
// Masa kunci sudah lewat: mulai hitungan dari nol
if ($gagal['kunci_sampai'] !== 0) {
    $gagal = ['jumlah' => 0, 'kunci_sampai' => 0];
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    unset($_SESSION['login_gagal'][$kunci]);

    // Ganti ID session setelah login (mencegah session fixation)
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    // Latihan 10.2: "Ingat Saya"
    if (!empty($_POST['ingat'])) {
        remember_buat($pdo, $user['id']);
    }

    header('Location: ../index.php');
    exit;
}

// Gagal: tambah hitungan (username yang tidak ada juga dihitung, supaya pesan tetap tidak membocorkan apa pun)
$gagal['jumlah']++;
if ($gagal['jumlah'] >= MAKS_GAGAL) {
    $gagal['kunci_sampai'] = time() + DURASI_KUNCI;
    $pesan = 'Terlalu banyak percobaan gagal. Login dikunci selama ' . DURASI_KUNCI . ' detik.';
} else {
    $pesan = 'Username atau password salah.';
    if ($gagal['jumlah'] >= PERINGATAN_MULAI) {
        $pesan .= ' Sisa percobaan: ' . (MAKS_GAGAL - $gagal['jumlah']) . '.';
    }
}

$_SESSION['login_gagal'][$kunci] = $gagal;
// Batasi ukuran data di session (kunci berasal dari input pengguna)
$_SESSION['login_gagal'] = array_slice($_SESSION['login_gagal'], -20, null, true);

$_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
header('Location: login.php');
exit;
