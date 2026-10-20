<?php
session_start();
require __DIR__ . '/includes/koneksi.php';

// Hanya menerima kiriman form (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Kosongkan tabel di database dan reset nomor id kembali ke 1
$pdo->exec("TRUNCATE TABLE buku, anggota RESTART IDENTITY");

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Semua data berhasil direset.'];
header('Location: index.php');
exit;
