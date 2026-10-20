<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Latihan 10.2: kalau belum ada sesi tapi ada cookie "Ingat Saya" yang valid, login otomatis
require_once __DIR__ . '/remember.php';
remember_login_otomatis();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Latihan 10.1: kontrol akses berbasis role.
// Panggil setelah require auth.php di halaman yang hanya boleh dibuka admin.
if (!function_exists('wajib_admin')) {
    function wajib_admin($alihKe = 'list.php')
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Hanya admin yang boleh melakukan aksi ini.'];
            header('Location: ' . $alihKe);
            exit;
        }
    }
}
