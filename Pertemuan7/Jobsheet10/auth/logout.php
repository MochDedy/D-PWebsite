<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Latihan 10.2: cabut token "Ingat Saya" (di DB dan di browser)
if (!empty($_COOKIE['remember'])) {
    require_once __DIR__ . '/../includes/remember.php';
    require __DIR__ . '/../includes/koneksi.php';
    remember_hapus($pdo);
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
