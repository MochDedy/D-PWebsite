<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

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
if ($no_anggota !== '') {
    $cek = $pdo->prepare("SELECT 1 FROM anggota WHERE LOWER(no_anggota) = LOWER(:no_anggota)");
    $cek->execute(['no_anggota' => $no_anggota]);
    if ($cek->fetchColumn()) {
        $errors[] = "No. Anggota sudah terdaftar.";
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

// ===== Valid: simpan ke database (prepared statement) =====
try {
    $stmt = $pdo->prepare(
        "INSERT INTO anggota (nama, no_anggota, alamat, no_hp, email, tanggal_lahir, jenis_kelamin)
        VALUES (:nama, :no_anggota, :alamat, :no_hp, :email, :tanggal_lahir, :jenis_kelamin)
        RETURNING id"
    );
    $stmt->execute([
        'nama' => $nama,
        'no_anggota' => $no_anggota,
        'alamat' => $alamat,
        'no_hp' => $no_hp,
        'email' => $email,
        'tanggal_lahir' => $tanggal_lahir,
        'jenis_kelamin' => $jenis_kelamin !== '' ? $jenis_kelamin : null,
    ]);
} catch (PDOException $e) {
    // SQLSTATE 23505 = unique_violation (no_anggota sudah dipakai)
    if ($e->getCode() === '23505') {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'No. Anggota sudah dipakai, gunakan nomor lain.'];
    } else {
        // Error lain: jangan bocorkan detail teknis ke pengguna
        error_log($e->getMessage());
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan saat menyimpan data. Coba lagi.'];
    }
    header('Location: tambah.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;
