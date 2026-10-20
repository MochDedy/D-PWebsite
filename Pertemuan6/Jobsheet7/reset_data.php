<?php
session_start();

// Hanya menerima kiriman form (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Kosongkan semua data session, lalu hancurkan sesi saat ini
$_SESSION = [];
session_destroy();

// Mulai sesi baru khusus untuk membawa pesan flash
session_start();
$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Semua data berhasil direset.'];
header('Location: index.php');
exit;
