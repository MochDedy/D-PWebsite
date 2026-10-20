<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$nama = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_anggota = trim($_POST['no_anggota'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
if ($jenis_kelamin !== 'P' && $jenis_kelamin !== 'L') {
    $jenis_kelamin = '';
}

// ===== Validasi server-side (sama dengan proses_tambah.php) =====
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
if ($no_hp !== '' && !preg_match('/^[0-9+\-\s]{8,15}$/', $no_hp)) {
    $errors[] = "No. HP hanya boleh berisi angka, +, -, spasi (8-15 karakter).";
}
// No. Anggota tidak boleh sama dengan anggota LAIN (baris ini sendiri dikecualikan)
if ($no_anggota !== '') {
    $cek = $pdo->prepare("SELECT 1 FROM anggota WHERE LOWER(no_anggota) = LOWER(:no_anggota) AND id <> :id");
    $cek->execute(['no_anggota' => $no_anggota, 'id' => $id]);
    if ($cek->fetchColumn()) {
        $errors[] = "No. Anggota sudah dipakai anggota lain.";
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit.php?id=' . urlencode($id));
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE anggota SET nama = :nama, no_anggota = :no_anggota, alamat = :alamat,
                        no_hp = :no_hp, email = :email, tanggal_lahir = :tanggal_lahir,
                        jenis_kelamin = :jenis_kelamin
     WHERE id = :id"
);
$stmt->execute([
    'nama' => $nama,
    'no_anggota' => $no_anggota,
    'alamat' => $alamat,
    'no_hp' => $no_hp,
    'email' => $email,
    'tanggal_lahir' => $tanggal_lahir,
    'jenis_kelamin' => $jenis_kelamin !== '' ? $jenis_kelamin : null,
    'id' => $id,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil diperbarui.'];
header('Location: list.php');
exit;
