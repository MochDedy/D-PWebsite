<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($username === '') {
    $errors[] = "Username wajib diisi.";
} elseif (strlen($username) > 50) {
    $errors[] = "Username maksimal 50 karakter.";
}
if (strlen($password) < 6) {
    $errors[] = "Password minimal 6 karakter.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: register.php');
    exit;
}

// Cek username duplikat (pesan ramah, bukan error mentah dari UNIQUE)
$cek = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$cek->execute(['username' => $username]);
if ($cek->fetch()) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username sudah digunakan.'];
    header('Location: register.php');
    exit;
}

// Role selalu 'petugas' (tidak diambil dari input pengguna)
$stmt = $pdo->prepare(
    "INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, 'petugas')"
);
$stmt->execute([
    'nama' => $nama,
    'username' => $username,
    'password' => password_hash($password, PASSWORD_DEFAULT),
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil. Silakan login.'];
header('Location: login.php');
exit;
