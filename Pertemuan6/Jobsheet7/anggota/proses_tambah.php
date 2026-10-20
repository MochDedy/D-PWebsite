<?php
session_start();

// Halaman ini hanya untuk menerima kiriman form (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$nama = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_anggota = trim($_POST['no_anggota'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

// Hanya terima nilai yang memang tersedia di form (P / L), selain itu dikosongkan
if ($jenis_kelamin !== 'P' && $jenis_kelamin !== 'L') {
    $jenis_kelamin = '';
}

// ===== Validasi server-side =====
$errors = [];

if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($email === '') {
    $errors[] = "Email wajib diisi.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}
if ($no_anggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}
if ($tanggal_lahir === '') {
    $errors[] = "Tanggal lahir wajib diisi.";
} else {
    $waktuLahir = strtotime($tanggal_lahir);
    if ($waktuLahir === false) {
        $errors[] = "Format tanggal lahir tidak valid.";
    } elseif ($waktuLahir > time()) {
        $errors[] = "Tanggal lahir tidak boleh di masa depan.";
    }
}
// No. HP opsional, tapi kalau diisi hanya angka, +, -, dan spasi (8-15 karakter)
if ($no_hp !== '' && !preg_match('/^[0-9+\-\s]{8,15}$/', $no_hp)) {
    $errors[] = "No. HP hanya boleh berisi angka, +, -, spasi (8-15 karakter).";
}
// No. Anggota tidak boleh sama dengan anggota yang sudah ada
foreach ($_SESSION['anggota'] ?? [] as $anggotaLama) {
    if (strcasecmp($anggotaLama['no_anggota'], $no_anggota) === 0) {
        $errors[] = "No. Anggota sudah terdaftar.";
        break;
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

// ===== Valid: simpan ke session =====
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

$_SESSION['anggota'][] = [
    'no_anggota' => $no_anggota,
    'nama' => $nama,
    'email' => $email,
    'tanggal_lahir' => $tanggal_lahir,
    'alamat' => $alamat,
    'no_hp' => $no_hp,
    'jenis_kelamin' => $jenis_kelamin,
];

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;
